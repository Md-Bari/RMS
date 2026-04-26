import { DashboardClient } from "@/components/admin/dashboard-client";

export default function AdminDashboardPage() {
  return (
    <main className="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-7xl">
        <DashboardClient />
      </div>
    </main>
  );
}
