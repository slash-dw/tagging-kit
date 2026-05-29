import '@testing-library/jest-dom/vitest';
import { afterEach } from 'vitest';
import { resetTaggingConfig } from '../src/config/store';

afterEach(() => {
  resetTaggingConfig();
});
