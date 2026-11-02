import mysql, { Pool, RowDataPacket, ResultSetHeader } from 'mysql2/promise';

const DB_CONFIG = {
  host: '127.0.0.1',
  port: 3309,
  user: 'root',
  password: 'mysql',
  database: 'studios_boomroom_site',
};

let pool: Pool | null = null;

function getPool(): Pool {
  if (!pool) {
    pool = mysql.createPool({ ...DB_CONFIG, waitForConnections: true, connectionLimit: 5, dateStrings: true, charset: 'utf8' });
  }
  return pool;
}

export async function query<T extends RowDataPacket[] = RowDataPacket[]>(sql: string, params: unknown[] = []): Promise<T> {
  const [rows] = await getPool().execute<T>(sql, params);
  return rows;
}

export async function execute(sql: string, params: unknown[] = []): Promise<ResultSetHeader> {
  const [result] = await getPool().execute<ResultSetHeader>(sql, params);
  return result;
}

export async function insertClient(opts: { name: string; card?: string; pass?: string; type?: number; dis?: number; phone?: string }): Promise<number> {
  const { name, card = '', pass = '', type = 1, dis = 0, phone = '+375290000000' } = opts;
  const contacts = JSON.stringify([{ name: '', phone, email: '', vk_id: '', vk_addr: '' }]);
  const res = await execute(
    "INSERT INTO `clients` (`type`, `name`, `note`, `card`, `pass`, `dis`, `state`, `contacts`) VALUES (?, ?, '', ?, ?, ?, 1, ?)",
    [type, name, card, pass, dis, contacts],
  );
  await execute("INSERT INTO `client_contacts` (`cid`, `name`, `phone`) VALUES (?, '', ?)", [res.insertId, phone]);
  return res.insertId;
}

export async function insertEvent(opts: { rid: number; date: string; start: number; end: number; cid?: number; state?: number; price?: number }): Promise<number> {
  const { rid, date, start, end, cid = 0, state = 0, price = 10 } = opts;
  const res = await execute(
    "INSERT INTO `events` (`rid`, `date`, `start`, `end`, `cid`, `note`, `eqs`, `price_eqs`, `price`, `pay`, `pay_date`, `dis`, `state`) VALUES (?, ?, ?, ?, ?, '', '[]', 0, ?, 0, '0000-00-00', 0, ?)",
    [rid, date, start, end, cid, price, state],
  );
  return res.insertId;
}

const DYNAMIC_TABLES = ['client_contacts', 'clients', 'event_weekly', 'events', 'expenses', 'incomes', 'log'];

export async function cleanTestData(): Promise<void> {
  for (const t of DYNAMIC_TABLES) {
    await query(`DELETE FROM \`${t}\``);
  }
}

export async function closePool(): Promise<void> {
  if (pool) {
    await pool.end();
    pool = null;
  }
}
