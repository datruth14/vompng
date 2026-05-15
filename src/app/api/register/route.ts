import { NextRequest, NextResponse } from 'next/server';
import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const { name, email, password, storeName, storeDescription, contactPhone } = body;

    // Validate input
    if (!name || !email || !password || !storeName || !contactPhone) {
      return NextResponse.json(
        { error: 'Missing required fields' },
        { status: 400 }
      );
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return NextResponse.json(
        { error: 'Invalid email format' },
        { status: 400 }
      );
    }

    // Check if user already exists
    const existingUser = await prisma.user.findUnique({
      where: { email },
    });

    if (existingUser) {
      return NextResponse.json(
        { error: 'Email already registered' },
        { status: 400 }
      );
    }

    // Hash password
    const saltRounds = 10;
    const hashedPassword = await bcrypt.hash(password, saltRounds);

    // Create store slug
    const storeSlug = storeName
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-|-$/g, '');

    // Check if store slug already exists
    const existingStore = await prisma.store.findUnique({
      where: { slug: storeSlug },
    });

    if (existingStore) {
      return NextResponse.json(
        { error: 'Store name already taken' },
        { status: 400 }
      );
    }

    // Create user and store in transaction
    const user = await prisma.user.create({
      data: {
        email,
        password: hashedPassword,
        name,
        stores: {
          create: {
            name: storeName,
            slug: storeSlug,
            description: storeDescription || '',
            contactPhone: contactPhone,
            isActive: true,
          },
        },
      },
      include: {
        stores: true,
      },
    });

    return NextResponse.json(
      {
        success: true,
        storeSlug: user.stores[0].slug,
        userId: user.id,
      },
      { status: 201 }
    );
  } catch (error) {
    console.error('Registration error:', error);
    return NextResponse.json(
      { error: 'Something went wrong. Please try again.' },
      { status: 500 }
    );
  } finally {
    await prisma.$disconnect();
  }
}
