import { expect } from '@playwright/test';
import { appendFileSync, mkdirSync } from 'fs';

// PHP output with display_errors=On (local and e2e images)
const PHP_ERROR = /(Fatal error|Parse error|Warning|Deprecated|Notice)(<\/b>)?:\s+([\s\S]*?) in (<b>)?\/var\/www\/html\/([^\s:<]+)(<\/b>)?(?::(\d+)| on line (<b>)?(\d+))/g;

// Messages that were notices on PHP 5.6 or are PHP 8.1+ deprecations: behaviour is unchanged,
// so they are reported (test-results/php-notices.log) but do not fail tests
const BENIGN = [
  /^Deprecated: /,
  /^Notice: /,
  /^Warning: Undefined (variable|global variable|array key|offset|index|property)/,
  /^Warning: Trying to access array offset on/,
  /^Warning: Attempt to read property ".*" on (null|bool|int|float|string|array|true|false)/,
  /^Warning: Uninitialized string offset/,
];

export function phpErrors(body: string): string[] {
  return [...body.matchAll(PHP_ERROR)].map((m) => `${m[1]}: ${m[3].replace(/<[^>]+>/g, '').split('\n')[0]} @ ${m[5]}:${m[7] ?? m[9]}`);
}

export function expectNoPhpErrors(body: string, context: string): void {
  const all = phpErrors(body);
  const benign = all.filter((e) => BENIGN.some((re) => re.test(e)));
  if (benign.length) {
    mkdirSync('test-results', { recursive: true });
    appendFileSync('test-results/php-notices.log', benign.map((e) => `${context}\t${e}\n`).join(''));
  }
  expect(all.filter((e) => !benign.includes(e)), `PHP errors on ${context}`).toEqual([]);
  expect(body, `DB error on ${context}`).not.toContain('Ошибка базы данных');
}
