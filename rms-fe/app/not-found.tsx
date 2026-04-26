import Link from "next/link";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";

export default function NotFound() {
  return (
    <main className="flex min-h-screen items-center justify-center px-4">
      <Card className="max-w-lg p-8 text-center">
        <p className="text-sm uppercase tracking-[0.25em] text-orange-500">404</p>
        <h1 className="mt-3 text-4xl font-bold text-slate-900">Page not found</h1>
        <p className="mt-3 text-slate-500">The page you requested is unavailable or the order could not be found.</p>
        <Link href="/menu" className="mt-6 inline-block">
          <Button>Back To Menu</Button>
        </Link>
      </Card>
    </main>
  );
}
