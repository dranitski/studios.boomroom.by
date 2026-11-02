import { test, expect } from '@playwright/test';
import { login } from './helpers/auth';
import { action, admin, formData } from './helpers/action';
import { cleanTestData, execute, insertClient, insertEvent, query } from './helpers/db';
import { daysFromNow, dmy, ymd } from './helpers/fixtures';

test.beforeEach(async ({ request }) => {
  await cleanTestData();
  await login(request, 'director');
});

const lastLog = async () => (await query('SELECT `text` FROM `log` ORDER BY `id` DESC LIMIT 1'))[0]?.text as string;

test.describe('schedule (table)', () => {
  test('new event form, save with a new client, get, note, pay, apply, delete', async ({ request }) => {
    const date = ymd(daysFromNow(3));
    const [tpl] = await query('SELECT `id` FROM `event_templates` WHERE `rid` = 1 AND `date` = \'0000-00-00\' LIMIT 1');
    const form = await action(request, admin('table', 'event', { option: 'new', tid: tpl.id, date }));
    expect(form).toContain('name="end"');

    await action(request, admin('table', 'event', {
      option: 'save', id: 0, rid: 1, date, start: 14, end: 16, cid: -1, nm: 0, client_type: 1,
      client_name: "O'Hara \\ Band", client_phone: '+375291112233', note: 'first <b>note</b>',
      eqs: JSON.stringify([{ id: 1, count: 2 }]), price: '30', price_eqs: '4', pay_type: 1, dis: 0, p: 0, free: 0,
    }));
    const [ev] = await query('SELECT * FROM `events` WHERE `date` = ? AND `rid` = 1', [date]);
    expect(ev).toMatchObject({ start: 14, end: 16, price: 30, state: 0, eqs: '[{"id":1,"count":2}]' });
    const [client] = await query('SELECT * FROM `clients` WHERE `id` = ?', [ev.cid]);
    expect(client.name).toBe('O&#039;Hara &#92; Band');
    expect(JSON.parse(client.contacts)[0].phone).toBe('+375291112233');
    expect(await lastLog()).toContain(`Добавление события. ID:${ev.id}`);

    const card = await action(request, admin('table', 'event', { option: 'get', id: ev.id }));
    expect(card).toContain('O&#039;Hara');

    await action(request, admin('table', 'event', { option: 'note', id: ev.id, note: 'second' }));
    await action(request, admin('table', 'event', { option: 'pay', id: ev.id, pay: '12,5' }));
    expect((await query('SELECT `note`, `pay` FROM `events` WHERE `id` = ?', [ev.id]))[0]).toMatchObject({ note: 'second', pay: 12.5 });

    await execute('UPDATE `events` SET `state` = 3 WHERE `id` = ?', [ev.id]);
    await action(request, admin('table', 'event', { option: 'apply', id: ev.id }));
    expect((await query('SELECT `state` FROM `events` WHERE `id` = ?', [ev.id]))[0].state).toBe(0);

    await action(request, admin('table', 'event', { option: 'delete', id: ev.id }));
    expect(await query('SELECT `id` FROM `events` WHERE `id` = ?', [ev.id])).toHaveLength(0);
  });

  test('edit an existing event and cancel it', async ({ request }) => {
    const cid = await insertClient({ name: 'Edit Band' });
    const id = await insertEvent({ rid: 2, date: ymd(daysFromNow(2)), start: 10, end: 12, cid });
    await action(request, admin('table', 'event', {
      option: 'save', id, rid: 2, date: ymd(daysFromNow(2)), start: 10, end: 13, cid, nm: 1, client_type: 1, client_name: '', client_phone: '',
      note: 'edited', eqs: '[]', price: '45', price_eqs: '0', pay_type: 2, dis: 10, p: 0, free: 0, apply: 1,
    }));
    expect((await query('SELECT `end`, `price`, `note`, `dis` FROM `events` WHERE `id` = ?', [id]))[0]).toMatchObject({ end: 13, price: 45, note: 'edited', dis: 10 });

    await action(request, admin('table', 'event', { option: 'cancel', id, type: 0 }));
    expect((await query('SELECT `state` FROM `events` WHERE `id` = ?', [id]))[0].state).toBe(4);
  });

  test('make weekly from an event, weekly get/save/nodate', async ({ request }) => {
    const cid = await insertClient({ name: 'Weekly Band' });
    const date = daysFromNow(4);
    const id = await insertEvent({ rid: 1, date: ymd(date), start: 19, end: 21, cid });
    await action(request, admin('table', 'event', { option: 'weekly', id }));
    const [w] = await query('SELECT * FROM `event_weekly` WHERE `cid` = ?', [cid]);
    expect(w).toMatchObject({ rid: 1, day: date.getDay(), start: 19, end: 21 });

    const next = ymd(daysFromNow(11));
    await action(request, admin('table', 'event', { option: 'weekly_get', id: w.id, date: next }));
    await action(request, admin('table', 'event', { option: 'weekly_save', id: w.id, date: next, note: 'one-off' }));
    expect(await query('SELECT `id` FROM `events` WHERE `date` = ? AND `cid` = ?', [next, cid])).toHaveLength(1);

    const later = ymd(daysFromNow(18));
    await action(request, admin('table', 'event', { option: 'weekly_nodate', id: w.id, date: later }));
    expect((await query('SELECT `nodates` FROM `event_weekly` WHERE `id` = ?', [w.id]))[0].nodates).toContain(later);
  });

  test('equipment availability, client lookup, requests', async ({ request }) => {
    const cid = await insertClient({ name: 'Lookup Band', phone: '+375297778899' });
    await action(request, admin('table', 'eqs', { option: 'get', rid: 1, date: ymd(daysFromNow(1)), start: 10, end: 12 }));
    expect(await action(request, admin('table', 'client', { option: 'get', id: cid }))).toContain('+375297778899');
    await action(request, admin('table', 'client', { option: 'findbyphone', number: '7778899' }));
    const reqId = await insertEvent({ rid: 1, date: ymd(daysFromNow(5)), start: 9, end: 11, cid, state: 3 });
    await action(request, admin('table', 'requests', { option: 'check' }));
    await action(request, admin('table', 'requests', { option: 'get' }));
    await action(request, admin('table', 'request', { option: 'apply', id: reqId }));
    expect((await query('SELECT `state` FROM `events` WHERE `id` = ?', [reqId]))[0].state).toBe(0);
  });
});

test.describe('weekly schedule', () => {
  test('new, save, get, delete, equipment', async ({ request }) => {
    const cid = await insertClient({ name: 'Weekly2' });
    const [tpl] = await query('SELECT `id` FROM `event_templates` WHERE `rid` = 7 AND `date` = \'0000-00-00\' LIMIT 1');
    await action(request, admin('weekly', 'event', { option: 'new', tid: tpl.id }));
    await action(request, admin('weekly', 'event', {
      option: 'save', id: 0, rid: 7, day: 2, start: 18, end: 20, cid, nm: 0, note: 'w', eqs: JSON.stringify([{ id: 2, count: 1 }]), price: '20', price_eqs: '0', dis: 0,
    }));
    const [w] = await query('SELECT * FROM `event_weekly` WHERE `cid` = ?', [cid]);
    expect(w).toMatchObject({ rid: 7, day: 2, start: 18, end: 20 });
    await action(request, admin('weekly', 'event', { option: 'get', id: w.id }));
    const res = await request.get('/action.php?rid=7&day=2&start=18&end=20', {});
    expect(res.status()).toBe(200);
    await action(request, admin('weekly', 'event', { option: 'delete', id: w.id }));
    expect((await query('SELECT `state` FROM `event_weekly` WHERE `id` = ?', [w.id]))[0]?.state ?? 0).not.toBe(1);
  });
});

test.describe('price templates (edittable)', () => {
  test('get, save, delete', async ({ request }) => {
    await action(request, admin('edittable', 'template', { option: 'get', id: 0, rid: 1, date: ymd(daysFromNow(1)), start: 9 }));
    await action(request, admin('edittable', 'template', { option: 'save', id: 0, rid: 1, date: ymd(daysFromNow(1)), start: 9, end: 11, weekly: 0, price_0: '1', price_1: '2', price_2: '3' }));
    const [t] = await query("SELECT * FROM `event_templates` WHERE `rid` = 1 AND `date` = ? AND `start` = 9", [ymd(daysFromNow(1))]);
    expect(t.price).toContain('3');
    await action(request, admin('edittable', 'template', { option: 'delete', id: t.id }));
    expect(await query('SELECT `id` FROM `event_templates` WHERE `id` = ?', [t.id])).toHaveLength(0);
  });
});

test.describe('clients', () => {
  test('add, edit with contacts, delete', async ({ request }) => {
    await action(request, admin('clients', 'add', {
      data: formData({ type: 2, name: "Ivan \"Bass\" O'Neil", note: 'n', card: '0999', pass: '9999', dis: 5, 'contact_id[]': [0], 'contact_name[]': ['Ivan'], 'contact_phone[]': ['+375290001122'], 'contact_email[]': ['a@b.c'], 'contact_vk_id[]': [''], 'contact_vk_addr[]': [''] }),
    }));
    const [c] = await query("SELECT * FROM `clients` WHERE `card` = '0999'");
    expect(c.name).toBe("Ivan \"Bass\" O'Neil");
    expect(JSON.parse(c.contacts)[0]).toMatchObject({ name: 'Ivan', phone: '+375290001122' });
    const [contact] = await query('SELECT * FROM `client_contacts` WHERE `cid` = ?', [c.id]);

    await action(request, admin('clients', 'edit', {
      id: c.id,
      data: formData({ type: 1, name: 'Renamed \\ Band', note: '', card: '0999', pass: '9999', dis: 0, 'contact_id[]': [contact.id, 0], 'contact_name[]': ['Ivan', 'Petr'], 'contact_phone[]': ['+375290001122', '+375290003344'], 'contact_email[]': ['', ''], 'contact_vk_id[]': ['', ''], 'contact_vk_addr[]': ['', ''] }),
    }));
    expect((await query('SELECT `name`, `type` FROM `clients` WHERE `id` = ?', [c.id]))[0]).toMatchObject({ name: 'Renamed \\ Band', type: 1 });
    expect(await query('SELECT `id` FROM `client_contacts` WHERE `cid` = ?', [c.id])).toHaveLength(2);

    await action(request, admin('clients', 'delete', { id: c.id }));
    expect(await query('SELECT `id` FROM `clients` WHERE `id` = ?', [c.id])).toHaveLength(0);
  });
});

test.describe('equipment, economics, debtors, params, statistics', () => {
  test('equipment add/edit/delete', async ({ request }) => {
    await action(request, admin('equipments', 'add', { data: formData({ sid: 1, name: "E2E 'amp'", price: 5, count: 2 }) }));
    const [e] = await query("SELECT * FROM `equipments` WHERE `name` LIKE 'E2E%'");
    expect(e).toMatchObject({ sid: 1, count: 2 });
    await action(request, admin('equipments', 'edit', { id: e.id, data: formData({ sid: 1, name: 'E2E amp 2', price: 6, count: 3 }) }));
    await action(request, admin('equipments', 'delete', { id: e.id }));
    expect(await query('SELECT `id` FROM `equipments` WHERE `id` = ?', [e.id])).toHaveLength(0);
  });

  test('expenses and incomes add/edit/delete', async ({ request }) => {
    for (const [kind, extra] of [['expenses', { type: 1, comment: "rent 'x'" }], ['incomes', { eid: 0, name: "lesson 'y'" }]] as const) {
      await action(request, admin('economics', kind, { option: 'add', data: formData({ sid: 1, aid: 1, sum: 77, date: dmy(daysFromNow(-1)), ...extra }) }));
      const [row] = await query(`SELECT * FROM \`${kind}\` WHERE \`sum\` = 77`);
      expect(row, kind).toBeDefined();
      await action(request, admin('economics', kind, { option: 'edit', id: row.id, data: formData({ sid: 1, aid: 2, sum: 78, date: dmy(daysFromNow(-1)), ...extra }) }));
      expect((await query(`SELECT \`sum\`, \`aid\` FROM \`${kind}\` WHERE \`id\` = ?`, [row.id]))[0]).toMatchObject({ sum: 78, aid: 2 });
      await action(request, admin('economics', kind, { option: 'delete', id: row.id }));
      expect(await query(`SELECT \`id\` FROM \`${kind}\` WHERE \`id\` = ?`, [row.id])).toHaveLength(0);
    }
  });

  test('debtors: save payment, delete', async ({ request }) => {
    const cid = await insertClient({ name: 'Debtor' });
    const id = await insertEvent({ rid: 1, date: ymd(daysFromNow(-5)), start: 10, end: 12, cid, state: 2, price: 40 });
    await execute("UPDATE `clients` SET `name` = 'Debtor\\'s band' WHERE `id` = ?", [cid]);
    await action(request, admin('debtors', 'savepay', { id, pay: '40' }));
    expect((await query('SELECT `sum`, `name` FROM `incomes` WHERE `eid` = ?', [id]))[0]).toMatchObject({ sum: 40, name: expect.stringContaining("Debtor's band") });
    await action(request, admin('debtors', 'delete', { id }));
  });

  test('params: holidays', async ({ request }) => {
    const before = (await query('SELECT `holidays` FROM `params`'))[0].holidays;
    await action(request, admin('params', 'holidays', { data: `${before},01.01.2031` }));
    expect((await query('SELECT `holidays` FROM `params`'))[0].holidays).toContain('01.01.2031');
    await execute('UPDATE `params` SET `holidays` = ?', [before]);
  });

  test('statistics charts', async ({ request }) => {
    const cid = await insertClient({ name: 'Stat Band' });
    await insertEvent({ rid: 1, date: ymd(daysFromNow(-3)), start: 10, end: 12, cid, state: 2 });
    for (const option of ['all', 'studios']) {
      for (const group of [1, 2, 3]) {
        await action(request, admin('statistics', 'chart', { option, id: 1, sid: 1, start: ymd(daysFromNow(-30)), end: ymd(daysFromNow(1)), group, ord: 0, sort: 0 }));
      }
    }
  });
});
