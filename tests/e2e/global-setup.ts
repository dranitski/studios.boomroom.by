import { cleanTestData, closePool } from './helpers/db';

export default async function globalSetup() {
  await cleanTestData();
  await closePool();
}
