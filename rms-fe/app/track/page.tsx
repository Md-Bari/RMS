import { Suspense } from "react";
import { TrackClient } from "@/components/menu/track-client";
import { Skeleton } from "@/components/ui/skeleton";

export default function TrackPage() {
  return (
    <Suspense
      fallback={
        <main className="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
          <div className="mx-auto max-w-4xl space-y-8">
            <Skeleton className="h-56 rounded-[32px]" />
            <Skeleton className="h-80 rounded-[32px]" />
          </div>
        </main>
      }
    >
      <TrackClient />
    </Suspense>
  );
}
