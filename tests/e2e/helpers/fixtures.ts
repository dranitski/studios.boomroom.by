import { execute, insertClient, insertEvent } from './db';

export const ymd = (d: Date) =>
  `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

export const dmy = (d: Date) =>
  `${String(d.getDate()).padStart(2, '0')}.${String(d.getMonth() + 1).padStart(2, '0')}.${d.getFullYear()}`;

export const daysFromNow = (n: number) => new Date(Date.now() + n * 86400_000);

// A small realistic data set so that admin pages render tables, totals and charts
export async function seedActivity(): Promise<{ clients: number[]; events: number[] }> {
  const clients = [
    await insertClient({ name: 'E2E Band', card: '0001', pass: '1111', type: 1 }),
    await insertClient({ name: 'E2E Drummer', card: '0002', pass: '2222', type: 2, dis: 10 }),
  ];
  const events: number[] = [];
  for (const [rid, day, start, end, cid, state] of [
    [1, -3, 10, 12, clients[0], 2],
    [1, -1, 18, 21, clients[1], 2],
    [2, 0, 12, 14, clients[0], 0],
    [7, 1, 20, 22, clients[1], 0],
    [10, 2, 9, 11, 0, 0],
    [1, -40, 15, 17, clients[0], 2],
  ] as const) {
    const id = await insertEvent({ rid, date: ymd(daysFromNow(day)), start, end, cid, state, price: 25 });
    // equipment rent feeds the economics report (economics?type=3)
    await execute('UPDATE `events` SET `eqs` = ?, `price_eqs` = 4 WHERE `id` = ?', [JSON.stringify([{ id: 1, count: 1 }, { id: 3, count: 2 }]), id]);
    if (state === 2) {
      await execute("UPDATE `events` SET `pay` = 25, `pay_date` = `date` WHERE `id` = ?", [id]);
      await execute("INSERT INTO `incomes` (`sid`, `aid`, `eid`, `name`, `sum`, `date`, `created`) VALUES (1, 1, ?, 'Репетиция', 25, ?, NOW())", [id, ymd(daysFromNow(day))]);
    }
    events.push(id);
  }
  await execute(
    "INSERT INTO `event_weekly` (`rid`, `day`, `start`, `end`, `cid`, `note`, `eqs`, `price_eqs`, `price`, `dis`, `nodates`, `state`) VALUES (1, 3, 19, 21, ?, '', '[]', 0, 30, 0, '', 1)",
    [clients[0]],
  );
  await execute("INSERT INTO `expenses` (`sid`, `aid`, `type`, `comment`, `sum`, `date`, `created`) VALUES (1, 1, 1, 'E2E аренда', 100, ?, NOW())", [ymd(daysFromNow(-2))]);
  await execute("INSERT INTO `log` (`text`) VALUES ('E2E log entry')");
  return { clients, events };
}
