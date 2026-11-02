import { APIRequestContext, expect } from '@playwright/test';

// Passwords from secrets-local.php
export const PASSWORDS = {
  admin: 'admin',
  admin_panel: 'panel',
  director: 'director',
} as const;

export type Role = keyof typeof PASSWORDS;

export async function login(request: APIRequestContext, role: Role, key: string = PASSWORDS[role]): Promise<void> {
  const res = await request.post('/auth', { form: { login: role, key }, maxRedirects: 0 });
  expect(res.status(), `login ${role}`).toBe(302);
  expect(res.headers()['location'], `login ${role} redirect`).toMatch(/^\/admin/);
}

export async function logout(request: APIRequestContext): Promise<void> {
  await request.post('/auth', { form: { logout: '1' }, maxRedirects: 0 });
}
