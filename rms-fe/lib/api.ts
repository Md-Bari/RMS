import { sampleMenuItems } from "@/lib/data/menu";
import type { MenuItemDTO } from "@/lib/types";

export const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL ?? "http://127.0.0.1:8000/api";
const API_ORIGIN = API_BASE_URL.replace(/\/api\/?$/, "");

export type BackendMenuPayload = {
  venue?: {
    venue_id: string;
    name: string;
    currency: string;
    welcome_banner?: string | null;
  };
  items: MenuItemDTO[];
};

export function normalizeImageUrl(input: string | undefined | null): string {
  const value = (input ?? "").trim();
  if (!value) return "";

  const duplicatedAbsolute = value.match(/^(https?:\/\/[^/]+)(https?:\/\/.+)$/i);
  const cleaned = duplicatedAbsolute ? duplicatedAbsolute[2] : value;

  if (cleaned.startsWith("http://") || cleaned.startsWith("https://")) {
    try {
      const parsed = new URL(cleaned);
      const apiOrigin = new URL(API_ORIGIN);

      // Fix previously saved localhost URLs without a port so they resolve to the active backend origin.
      if (parsed.hostname === "localhost" && !parsed.port && parsed.pathname.startsWith("/storage/")) {
        return `${apiOrigin.origin}${parsed.pathname}${parsed.search}`;
      }
    } catch {
      return cleaned;
    }

    return cleaned;
  }

  if (cleaned.startsWith("//")) {
    return `http:${cleaned}`;
  }

  if (cleaned.startsWith("/")) {
    return `${API_ORIGIN}${cleaned}`;
  }

  return cleaned;
}

export async function getBackendMenu(): Promise<BackendMenuPayload> {
  try {
    const venueId = process.env.NEXT_PUBLIC_DEMO_VENUE_ID;
    const endpoint = venueId ? `/venues/${venueId}/menu` : "/menu";

    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      next: { revalidate: 15 }
    });

    if (!response.ok) {
      return { items: sampleMenuItems };
    }

    const payload = (await response.json()) as BackendMenuPayload;

    return {
      ...payload,
      items: payload.items.map((item) => {
        const normalizedImage = normalizeImageUrl(item.imageUrl || item.image);

        return {
          ...item,
          image: normalizedImage,
          imageUrl: normalizedImage
        };
      })
    };
  } catch {
    return { items: sampleMenuItems };
  }
}
