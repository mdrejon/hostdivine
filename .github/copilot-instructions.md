# Copilot instructions

All project instructions live in [AGENTS.md](../AGENTS.md) at the repository root (shared by every AI agent). Read and follow it in full.

Key rules in brief:
- **Breakpoints:** use only 575.98 / 767.98 / 991.98 / 1199.98 (`max-width`) and 1400 / 1920 (`min-width`). The base styles are Desktop (1200–1399). Don't add other breakpoints.
- **Overflow:** there must be no horizontal scroll at any width from 320px. Check pages with `tools/responsive-check.html?page=<file>`.
- **Short screens:** the first screen must fit short laptop screens (1366×650, 1280×560). Reuse the `fitHero` pattern in `assets/js/main.js`.
- **Code:** plain HTML, CSS and vanilla JS. Use the single stylesheet `assets/css/style.css` and its tokens. JS attaches to `data-*` attributes.
- **Reference only:** never copy from `Hostingard/`, which is a third-party theme.
