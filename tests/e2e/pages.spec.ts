import { test, expect } from '@playwright/test';
import { login, Role } from './helpers/auth';
import { cleanTestData, query } from './helpers/db';
import { seedActivity, dmy, daysFromNow } from './helpers/fixtures';
import { expectNoPhpErrors } from './helpers/php';

let clientId: number;

test.beforeAll(async () => {
  await cleanTestData();
  ({ clients: [clientId] } = await seedActivity());
});

test('all public pages from `links` render without PHP errors', async ({ request }) => {
  const links = await query("SELECT `url` FROM `links` WHERE `link` NOT LIKE 'ext=admin%' AND `link` NOT LIKE 'ext=user%'");
  expect(links.length).toBeGreaterThan(30);
  for (const { url } of links) {
    const res = await request.get(`/${url}`);
    expect(res.status(), url).toBe(200);
    expectNoPhpErrors(await res.text(), `/${url}`);
  }
});

test('home, booking with params and 404 render without PHP errors', async ({ request }) => {
  const today = dmy(new Date());
  for (const url of ['/', '/booking?sid=3', '/booking?sid=4&rid=10', `/booking?date=${dmy(daysFromNow(7))}`, `/booking-rooms?date=${today}`, '/repaby?sid=1&rid=2', '/no-such-page']) {
    const res = await request.get(url);
    expect(res.status(), url).toBeLessThan(500);
    expectNoPhpErrors(await res.text(), url);
  }
});

const SCHEDULE_PAGES = [
  '/admin/table', '/admin/table?rid=2', '/admin/table?rid=7', '/admin/table?rid=10',
  `/admin/table?rid=1&date=${dmy(daysFromNow(-3))}`,
  '/admin/weekly', '/admin/weekly?rid=7',
  '/admin/edittable', '/admin/edittable?rid=10',
  '/admin/tablerooms', `/admin/tablerooms?date=${dmy(daysFromNow(1))}`,
  '/admin/clients', '/admin/clients?type=1', '/admin/clients?type=2', '/admin/clients?act=add',
  '/admin/equipments', '/admin/debtors', '/admin/log', '/admin/log?date_d1=01.01.2020&date_d2=31.12.2030',
  '/admin/statistics', '/admin/statistics?type=1', '/admin/statistics?type=2', '/admin/statistics?type=3', '/admin/statistics?type=4',
  '/admin/economics', '/admin/economics?type=1', '/admin/economics?type=2', '/admin/economics?type=3', '/admin/economics?type=4',
  '/admin/params',
];

const SITE_PAGES = ['/admin', '/admin/pages', '/admin/pages?act=add', '/admin/pages?edit=4', '/admin/pages?edit=33', '/admin/pages?edit=18', '/admin/blocks', '/admin/blocks?edit=1', '/admin/blocks?edit=5', '/admin/blocks?edit=7'];

const PAGES_BY_ROLE: Record<Role, string[]> = {
  director: SCHEDULE_PAGES,
  admin_panel: SCHEDULE_PAGES,
  admin: SITE_PAGES,
};

for (const role of Object.keys(PAGES_BY_ROLE) as Role[]) {
  test(`${role}: admin pages render without PHP errors`, async ({ request }) => {
    test.setTimeout(120_000);
    await login(request, role);
    const urls = [...PAGES_BY_ROLE[role], `/admin/clients?edit=${clientId}`];
    for (const url of urls) {
      const res = await request.get(url);
      expect(res.status(), url).toBe(200);
      const body = await res.text();
      expect(body, url).not.toContain('Вход в панель администрирования');
      expectNoPhpErrors(body, url);
    }
  });
}
