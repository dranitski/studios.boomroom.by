import { test, expect, APIRequestContext } from '@playwright/test';
import { readFileSync } from 'fs';
import { login } from './helpers/auth';
import { action } from './helpers/action';
import { cleanTestData, insertClient, query } from './helpers/db';
import { daysFromNow, ymd } from './helpers/fixtures';
import { expectNoPhpErrors } from './helpers/php';

const LOGIN_FORM = 'Вход в панель администрирования';

test.beforeEach(async () => {
  await cleanTestData();
});

const booking = (request: APIRequestContext, form: Record<string, string | number>, opts: { status?: number } = {}) =>
  action(request, { ext: 'booking', ...form }, opts);

test.describe('routing', () => {
  test('ext/view path traversal does not reach admin views', async ({ request }) => {
    await insertClient({ name: 'Secret Client', card: '4242', pass: '4242' });
    for (const url of [
      '/index.php?ext=booking&view=main%2F..%2F..%2Fadmin%2Fclients',
      '/index.php?ext=content&view=..%2Fadmin%2Fclients',
      '/index.php?ext=admin%2Fclients',
      '/index.php?ext=user&view=..%2Fadmin%2Fdebtors',
    ]) {
      const body = await (await request.get(url)).text();
      expect(body, url).not.toContain('Secret Client');
      expectNoPhpErrors(body, url);
    }
    const res = await request.post('/action.php', { form: { ext: '../exts/admin', task: 'clients', subtask: 'delete', id: '1' } });
    expect(await res.text()).toBe('');
  });

  test('moxiecut file manager is gone', async ({ request }) => {
    // missing files fall through to the site's 404 page (served with status 200)
    for (const url of ['/plugins/tinymce/plugins/moxiecut/api.php?action=listFiles', '/plugins/tinymce/plugins/moxiecut/plugin.min.js']) {
      const body = await (await request.get(url)).text();
      expect(body, url).not.toMatch(/moxman|"result"|tinymce\.PluginManager/);
    }
  });

  test('array parameters do not crash input parsing', async ({ request }) => {
    const res = await request.get('/booking?date[]=1&sid[]=2');
    expectNoPhpErrors(await res.text(), 'array params');
    const res2 = await request.post('/action.php', { form: { 'ext[]': 'booking', 'task[]': 'client' } });
    expectNoPhpErrors(await res2.text(), 'array params POST');
  });
});

test.describe('public booking', () => {
  test('card login is not injectable and client data needs a valid card', async ({ request }) => {
    const cid = await insertClient({ name: 'Card Owner', card: '1234', pass: '5678', phone: '+375291234567' });

    for (const [card, pass] of [["\\", " UNION SELECT group_concat(card,0x3a,pass) FROM clients#"], ["' OR '1'='1", "' OR '1'='1"], ['1234', '0000']]) {
      const body = await booking(request, { task: 'client', subtask: 'check', card, pass });
      expect(body, `${card}/${pass}`).not.toContain('data-state');
    }
    await booking(request, { task: 'client', subtask: 'get', id: cid }, { status: 403 });

    const ok = await booking(request, { task: 'client', subtask: 'check', card: '1234', pass: '5678' });
    expect(ok).toContain(`data('id', ${cid})`);
    expect(await booking(request, { task: 'client', subtask: 'get', id: cid })).toContain('Card Owner');

    const other = await insertClient({ name: 'Other Client', card: '9999', pass: '9999' });
    await booking(request, { task: 'client', subtask: 'get', id: other }, { status: 403 });
  });

  test('booking request: equipment is sanitized, client id needs a card check, bad hours rejected', async ({ request }) => {
    const victim = await insertClient({ name: 'Victim', card: '1111', pass: '1111' });
    const date = ymd(daysFromNow(6));
    await booking(request, {
      task: 'event', subtask: 'save', rid: 1, date, start: 12, end: 14, nm: 0, client_type: 1,
      client_name: "Anon 'Band'", client_phone: '+375290000001', cid: victim, note: 'hi',
      eqs: `[{"id":"1<svg onload=alert(1)>","count":"2'),(1,1)#"},{"id":3,"count":1}]`, price: '30',
    });
    const [ev] = await query('SELECT * FROM `events` WHERE `date` = ? AND `rid` = 1', [date]);
    expect(ev).toMatchObject({ state: 3, cid: 0, eqs: '[{"id":1,"count":2},{"id":3,"count":1}]' });
    expect(ev.note).toContain('Anon &#039;Band&#039;');

    const started = Date.now();
    await booking(request, { task: 'event', subtask: 'save', rid: 1, date, start: 20, end: 20, nm: 0, client_type: 1, client_name: 'x', client_phone: '1', cid: 0, eqs: '[]', price: '1' }, { status: 400 });
    expect(Date.now() - started).toBeLessThan(5000);

    const eqs = await request.get(`/action.php?ext=booking&task=eqs&subtask=get&rid=1&date=${date}&start=20&end=18`, { timeout: 10_000 });
    expect(eqs.status()).toBeLessThan(500);
  });

  test('a client can book under their own card after the check', async ({ request }) => {
    const cid = await insertClient({ name: 'Own Card', card: '2468', pass: '1357' });
    await booking(request, { task: 'client', subtask: 'check', card: '2468', pass: '1357' });
    const date = ymd(daysFromNow(8));
    await booking(request, { task: 'event', subtask: 'save', rid: 2, date, start: 10, end: 12, nm: 0, client_type: 1, client_name: '', client_phone: '', cid, note: '', eqs: '[]', price: '20' });
    expect((await query('SELECT `cid`, `state` FROM `events` WHERE `date` = ? AND `rid` = 2', [date]))[0]).toMatchObject({ cid, state: 3 });
  });
});

test.describe('admin endpoints', () => {
  test('block settings cannot be changed anonymously', async ({ request }) => {
    for (const block of ['menu', 'menu_top', 'menu_bottom', 'menu_left', 'menu_sub1', 'menu_sub2', 'slider', 'feedback', 'enroll', 'question']) {
      const xml = `blocks/${block}/admin.xml`;
      let before = '';
      try { before = readFileSync(xml, 'utf8'); } catch { /* block without settings file */ }
      const res = await request.post(`/blocks/${block}/action.php`, { form: { task: 'save', bid: '1', r: '1', email: 'evil@example.com', names: 'x', links: '//evil.example', ords: '1' } });
      expect(res.status(), block).toBe(403);
      let after = '';
      try { after = readFileSync(xml, 'utf8'); } catch { /* none */ }
      expect(after, block).toBe(before);
    }
  });

  test('admin actions require a role and POST', async ({ request }) => {
    const cid = await insertClient({ name: 'Keep Me' });
    await request.post('/action.php', { form: { ext: 'admin', task: 'clients', subtask: 'delete', id: String(cid) } });
    expect(await query('SELECT id FROM `clients` WHERE id = ?', [cid])).toHaveLength(1);

    await login(request, 'director');
    await request.get(`/action.php?ext=admin&task=clients&subtask=delete&id=${cid}`);
    expect(await query('SELECT id FROM `clients` WHERE id = ?', [cid]), 'GET must not run actions').toHaveLength(1);
  });

  test('stored values are escaped for SQL on the second hop (make weekly, debt, logs)', async ({ request }) => {
    await login(request, 'director');
    const cid = await insertClient({ name: "Rock'n'Roll \\ Band" });
    const date = ymd(daysFromNow(9));
    await action(request, { ext: 'admin', task: 'table', subtask: 'event', option: 'save', id: 0, rid: 1, date, start: 12, end: 14, cid, nm: 0, client_type: 1, client_name: '', client_phone: '', note: "it's", eqs: '[]', price: '10', price_eqs: '0', pay_type: 1, dis: 0, p: 0, free: 0 });
    const [ev] = await query('SELECT id FROM `events` WHERE `date` = ?', [date]);
    await action(request, { ext: 'admin', task: 'table', subtask: 'event', option: 'weekly', id: ev.id });
    expect(await query('SELECT id FROM `event_weekly` WHERE `cid` = ?', [cid])).toHaveLength(1);
    const logs = await query("SELECT `text` FROM `log` WHERE `text` LIKE '%Rock%'");
    expect(logs.length).toBeGreaterThanOrEqual(2);
  });
});

test.describe('output escaping', () => {
  test('reflected values are escaped', async ({ request }) => {
    const nf = await (await request.get('/no-such-page-<script>alert(1)</script>')).text();
    expect(nf).not.toContain('<script>alert(1)</script>');
    const links = await query("SELECT `url` FROM `links` WHERE `link` LIKE 'ext=content&view=page&id=2'");
    const contacts = await (await request.get(`/${links[0].url}?result=%22%3E%3Cscript%3Ealert(1)%3C/script%3E`)).text();
    expect(contacts).not.toContain('<script>alert(1)</script>');
  });
});
