import { useQuery } from '@tanstack/react-query';
import { fetchTaggingConfig } from '../api/taggingClient';
import { applyBackendDefaults, configureTaggingUi, getTaggingConfig } from '../config/store';

export interface UseTaggingBootstrapOptions {
  /** API base (e.g. VITE_API_URL). Stored so suggest/delete share it. */
  baseUrl?: string;
  enabled?: boolean;
}

/**
 * Fetch backend defaults (GET {base}/tagging/config) once at app boot and apply
 * them to the internal store (priority 3 — host `configureTaggingUi` overrides
 * win). Mirrors the host's existing bootstrap hooks (e.g. useLocalesBootstrap).
 */
export function useTaggingBootstrap(options: UseTaggingBootstrapOptions = {}) {
  const { baseUrl, enabled = true } = options;

  if (baseUrl !== undefined && baseUrl !== getTaggingConfig().baseUrl) {
    configureTaggingUi({ baseUrl });
  }

  return useQuery({
    queryKey: ['tagging-kit-config', getTaggingConfig().baseUrl],
    queryFn: async () => {
      const { baseUrl: base, fetch } = getTaggingConfig();
      const cfg = await fetchTaggingConfig(base, fetch);
      applyBackendDefaults(cfg);

      return cfg;
    },
    enabled,
    staleTime: 60 * 60 * 1000,
    gcTime: Number.POSITIVE_INFINITY,
  });
}
