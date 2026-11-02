import { test, expect } from '@playwright/test';
import { login, logout, PASSWORDS, Role } from './helpers/auth';

const ROLES = Object.keys(PASSWORDS) as Role[];

async function isLoggedIn(request: import('@playwright/test').APIRequestContext): Promise<boolean> {
  const body = await (await request.get('/admin/table')).text();
  return !body.includes('Вход в панель администрирования');
}

test('anonymous visitor sees the login form', async ({ request }) => {
  expect(await isLoggedIn(request)).toBe(false);
});

for (const role of ROLES) {
  test(`${role}: login, session cookie is signed and HttpOnly, logout`, async ({ request }) => {
    await login(request, role);
    const state = await request.storageState();
    const cookie = state.cookies.find((c) => c.name === `user_${role}`);
    expect(cookie, 'auth cookie').toBeDefined();
    expect(cookie!.value).toMatch(/^\d+\.[0-9a-f]{64}$/);
    expect(cookie!.httpOnly).toBe(true);
    expect(await isLoggedIn(request)).toBe(true);

    await logout(request);
    expect(await isLoggedIn(request)).toBe(false);
  });
}

test('wrong password is rejected', async ({ request }) => {
  const res = await request.post('/auth', { form: { login: 'director', key: 'wrong' }, maxRedirects: 0 });
  expect(res.status()).toBe(302);
  expect(await isLoggedIn(request)).toBe(false);
});

test('unknown login is rejected even with a known password', async ({ request }) => {
  await request.post('/auth', { form: { login: 'root', key: PASSWORDS.director }, maxRedirects: 0 });
  expect(await isLoggedIn(request)).toBe(false);
});

for (const role of ROLES) {
  test(`${role}: forged cookies do not authenticate`, async ({ playwright, baseURL }) => {
    for (const value of ['1', 'true', `${Math.floor(Date.now() / 1000) + 3600}.${'0'.repeat(64)}`]) {
      const ctx = await playwright.request.newContext({
        baseURL,
        extraHTTPHeaders: { Cookie: `user_${role}=${value}` },
      });
      expect(await isLoggedIn(ctx), `user_${role}=${value}`).toBe(false);
      await ctx.dispose();
    }
  });
}

test('expired token is rejected', async ({ request, playwright, baseURL }) => {
  await login(request, 'director');
  const cookie = (await request.storageState()).cookies.find((c) => c.name === 'user_director')!;
  const [, sig] = cookie.value.split('.');
  const ctx = await playwright.request.newContext({ baseURL, extraHTTPHeaders: { Cookie: `user_director=1000.${sig}` } });
  expect(await isLoggedIn(ctx)).toBe(false);
  await ctx.dispose();
});

test('token of one role does not grant another role', async ({ request, playwright, baseURL }) => {
  await login(request, 'admin_panel');
  const cookie = (await request.storageState()).cookies.find((c) => c.name === 'user_admin_panel')!;
  const ctx = await playwright.request.newContext({ baseURL, extraHTTPHeaders: { Cookie: `user_director=${cookie.value}` } });
  expect(await isLoggedIn(ctx)).toBe(false);
  await ctx.dispose();
});

test('browser login through the form', async ({ page }) => {
  await page.goto('/admin');
  await page.selectOption('select[name=login]', 'director');
  await page.fill('input[name=key]', PASSWORDS.director);
  await page.click('input[type=submit]');
  await expect(page).toHaveURL(/\/admin\/table/);
  await expect(page.locator('#head')).toBeVisible();
});
