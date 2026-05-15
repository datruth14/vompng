import { promises as fs } from 'fs';
import path from 'path';

const DB_DIR = path.resolve(process.cwd(), 'data');
const DB_PATH = path.resolve(DB_DIR, 'db.json');

async function ensureDb() {
  try {
    await fs.access(DB_PATH);
  } catch {
    await fs.mkdir(DB_DIR, { recursive: true });
    await fs.writeFile(DB_PATH, JSON.stringify([]), 'utf8');
  }
}

async function readJson<T = any>(): Promise<T> {
  await ensureDb();
  const content = await fs.readFile(DB_PATH, 'utf8');
  try {
    return JSON.parse(content) as T;
  } catch {
    // If corrupted, reset
    await fs.writeFile(DB_PATH, JSON.stringify([]), 'utf8');
    return [] as unknown as T;
  }
}

async function writeJson(data: any): Promise<void> {
  await ensureDb();
  await fs.writeFile(DB_PATH, JSON.stringify(data, null, 2), 'utf8');
}

export async function getAll(): Promise<any[]> {
  const data = await readJson<any[]>();
  if (Array.isArray(data)) return data;
  return [];
}

export async function addItem(item: any): Promise<any> {
  const items = await getAll();
  items.push(item);
  await writeJson(items);
  return item;
}

export async function updateItem(id: any, patch: any): Promise<any> {
  const items = await getAll();
  const idx = items.findIndex((i) => i.id === id);
  if (idx === -1) throw new Error('Item not found');
  items[idx] = { ...items[idx], ...patch };
  await writeJson(items);
  return items[idx];
}

export async function deleteItem(id: any): Promise<void> {
  const items = await getAll();
  const next = items.filter((i) => i.id !== id);
  await writeJson(next);
}
