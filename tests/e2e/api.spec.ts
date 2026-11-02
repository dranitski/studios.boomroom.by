import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';
import { execute, insertClient, insertEvent, query } from './helpers/db';

// Cron calls api.php from the server itself; inside the container that is 127.0.0.1
function cron(task: string): string {
  return execSync(
    `docker compose -f docker-compose.e2e.yaml exec -T e2e-www curl -s -w ' HTTP%{http_code}' "http://127.0.0.1/api.php?task=${task}"`,
    { encoding: 'utf8' },
  );
}

const ymd = (d: Date) => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

test.beforeEach(async () => {
  await execute('DELETE FROM `events`');
  await execute('DELETE FROM `event_weekly`');
});

test('api.php is closed for outside requests', async ({ request }) => {
  for (const task of ['stateEvents', 'createEventsToday', 'cleanLog', 'getItem&table=clients&id=1', 'getTasks', 'getFreeTime']) {
    const res = await request.get(`/api.php?task=${task}`);
    expect(res.status(), task).toBe(403);
    expect(await res.text(), task).toBe('');
  }
});

test('api.php ignores X-Forwarded-For / X-Real-IP spoofing', async ({ request }) => {
  const res = await request.get('/api.php?task=stateEvents', { headers: { 'X-Real-IP': '127.0.0.1', 'X-Forwarded-For': '127.0.0.1' } });
  expect(res.status()).toBe(403);
});

test('cron createEventsToday creates today events from weekly bookings', async () => {
  const today = new Date();
  const cid = await insertClient({ name: 'Weekly Band' });
  await execute(
    "INSERT INTO `event_weekly` (`rid`, `day`, `start`, `end`, `cid`, `note`, `eqs`, `price_eqs`, `price`, `dis`, `nodates`, `state`) VALUES (1, ?, 18, 20, ?, 'weekly', '[]', 0, 30, 0, '', 1)",
    [today.getDay(), cid],
  );
  expect(cron('createEventsToday')).toMatch(/HTTP200$/);
  const events = await query('SELECT * FROM `events` WHERE `date` = ? AND `rid` = 1 AND `start` = 18', [ymd(today)]);
  expect(events).toHaveLength(1);
  expect(events[0].cid).toBe(cid);

  cron('createEventsToday');
  expect(await query('SELECT id FROM `events` WHERE `date` = ?', [ymd(today)]), 'no duplicates').toHaveLength(1);
});

test('cron stateEvents closes past events', async () => {
  const past = new Date(Date.now() - 2 * 86400_000);
  const future = new Date(Date.now() + 2 * 86400_000);
  const pastId = await insertEvent({ rid: 1, date: ymd(past), start: 10, end: 12, state: 0 });
  const futureId = await insertEvent({ rid: 1, date: ymd(future), start: 10, end: 12, state: 0 });
  expect(cron('stateEvents')).toMatch(/HTTP200$/);
  const rows = await query('SELECT id, state FROM `events` WHERE id IN (?, ?) ORDER BY id', [pastId, futureId]);
  expect(rows.map((r) => r.state)).toEqual([2, 0]);
});

test('cron cleanLog keeps 3 years of the action log', async () => {
  await execute('DELETE FROM `log`');
  await execute("INSERT INTO `log` (`text`, `created`) VALUES ('old', NOW() - INTERVAL 3 YEAR - INTERVAL 1 DAY), ('recent', NOW() - INTERVAL 3 YEAR + INTERVAL 1 DAY), ('today', NOW())");
  expect(cron('cleanLog')).toMatch(/HTTP200$/);
  expect((await query('SELECT `text` FROM `log` ORDER BY `id`')).map((r) => r.text)).toEqual(['recent', 'today']);
});
