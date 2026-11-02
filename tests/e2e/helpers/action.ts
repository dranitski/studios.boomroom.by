import { APIRequestContext, expect } from '@playwright/test';
import { expectNoPhpErrors } from './php';

// POST /action.php as the app's jQuery code does (urlencoded form)
export async function action(request: APIRequestContext, form: Record<string, string | number>, opts: { status?: number } = {}): Promise<string> {
  const data = Object.fromEntries(Object.entries(form).map(([k, v]) => [k, String(v)]));
  const res = await request.post('/action.php', { form: data, maxRedirects: 0 });
  const body = await res.text();
  const label = Object.values(data).slice(0, 4).join('/');
  expect(res.status(), `${label}: ${body.slice(0, 300)}`).toBe(opts.status ?? 200);
  expectNoPhpErrors(body, `action ${label}`);
  expect(body, `${label}: DB error`).not.toContain('Ошибка базы данных');
  return body;
}

export const admin = (task: string, subtask: string, extra: Record<string, string | number> = {}) => ({ ext: 'admin', task, subtask, ...extra });

// jQuery serializeArray() format used by the admin forms
export const formData = (fields: Record<string, string | number | (string | number)[]>) =>
  JSON.stringify(
    Object.entries(fields).flatMap(([name, value]) => (Array.isArray(value) ? value.map((v) => ({ name, value: String(v) })) : [{ name, value: String(value) }])),
  );
