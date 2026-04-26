import { Skeleton } from "@/components/ui/skeleton";

export default function MenuLoading() {
  return (
    <main className="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-7xl space-y-8">
        <Skeleton className="h-24 w-full rounded-[32px]" />
        <Skeleton className="h-72 w-full rounded-[36px]" />
        <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
          {Array.from({ length: 6 }).map((_, index) => (
            <Skeleton key={index} className="h-[520px] rounded-[28px]" />
          ))}
        </div>
      </div>
    </main>
  );
}
