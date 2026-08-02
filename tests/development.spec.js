// development/ 配下の静的ページを横断検証
import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, '..');
const DEV_ROOT = path.join(ROOT, 'development');

/** @returns {string[]} */
function collectHtmlFiles(dir, base = '') {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  /** @type {string[]} */
  const files = [];
  for (const entry of entries) {
    const rel = path.posix.join(base.replace(/\\/g, '/'), entry.name);
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...collectHtmlFiles(full, rel));
    } else if (entry.name.endsWith('.html')) {
      files.push(rel);
    }
  }
  return files.sort();
}

const htmlFiles = collectHtmlFiles(DEV_ROOT);
const pagePaths = htmlFiles.map(f => `/development/${f.replace(/\\/g, '/')}`);

test.describe('development 静的ページ', () => {
  for (const pagePath of pagePaths) {
    test(`${pagePath} が正常に表示される`, async ({ page }) => {
      const consoleErrors = [];
      page.on('console', msg => {
        if (msg.type() === 'error') {
          consoleErrors.push(msg.text());
        }
      });
      page.on('pageerror', err => {
        consoleErrors.push(err.message);
      });

      const response = await page.goto(pagePath, { waitUntil: 'domcontentloaded' });
      expect(response?.status(), 'HTTPステータス').toBe(200);

      // 開発トップのローダー完了を待つ
      if (pagePath === '/development/index.html') {
        await page.waitForFunction(
          () => !document.body.classList.contains('is-loading'),
          null,
          { timeout: 5000 }
        ).catch(() => {});
      }

      await expect(page.locator('body')).toBeVisible();
      const title = await page.title();
      expect(title.length, 'title が空').toBeGreaterThan(0);

      const hasMain = (await page.locator('main').count()) > 0;
      const hasHero = (await page.locator('section.hero, section.portal-desk, section.demo-video-hero, .step-container').count()) > 0;
      expect(hasMain || hasHero, 'main または主要コンテンツ領域').toBeTruthy();

      // 内部リンク切れ（development 配下・同一オリジン）
      const broken = await page.evaluate(async () => {
        const origin = window.location.origin;
        const anchors = [...document.querySelectorAll('a[href]')];
        /** @type {string[]} */
        const failures = [];
        for (const a of anchors) {
          const href = a.getAttribute('href');
          if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('http')) {
            continue;
          }
          if (a.hasAttribute('data-asset-optional')) {
            continue;
          }
          let url;
          try {
            url = new URL(href, window.location.href);
          } catch {
            failures.push(`invalid href: ${href}`);
            continue;
          }
          if (url.origin !== origin) continue;
          if (!url.pathname.includes('/development/') && !url.pathname.endsWith('.html') && url.pathname !== '/') {
            continue;
          }
          try {
            const res = await fetch(url.href, { method: 'HEAD' });
            if (res.status === 405) {
              const getRes = await fetch(url.href, { method: 'GET' });
              if (!getRes.ok) failures.push(`${href} → ${getRes.status}`);
            } else if (!res.ok && res.status !== 404) {
              const getRes = await fetch(url.href, { method: 'GET' });
              if (!getRes.ok) failures.push(`${href} → ${getRes.status}`);
            } else if (res.status === 404) {
              failures.push(`${href} → 404`);
            }
          } catch (e) {
            failures.push(`${href} → ${e.message}`);
          }
        }
        return failures;
      });

      expect(broken, '内部リンク切れ').toEqual([]);

      // 主要アセット（stylesheet）
      const cssHrefs = await page.$$eval('link[rel="stylesheet"]', links =>
        links.map(l => l.getAttribute('href')).filter(Boolean)
      );
      for (const href of cssHrefs) {
        const cssUrl = new URL(href, page.url()).href;
        const cssRes = await page.request.get(cssUrl);
        expect(cssRes.status(), `CSS: ${href}`).toBe(200);
      }

      const ignorableConsole = consoleErrors.filter(
        e => !e.includes('favicon') && !e.includes('Failed to load resource')
      );
      expect(ignorableConsole, 'コンソールエラー').toEqual([]);
    });
  }
});

test.describe('development ナビゲーション', () => {
  test('開発トップから主要カテゴリへ遷移できる', async ({ page }) => {
    await page.goto('/development/index.html');
    await page.waitForFunction(
      () => !document.body.classList.contains('is-loading'),
      null,
      { timeout: 5000 }
    ).catch(() => {});

    const links = [
      { name: 'シミュレーション', path: '/development/simulation/' },
      { name: 'アプリ開発', path: '/development/app/' },
      { name: 'モデリング', path: '/development/modeling/' },
      { name: '発表ポスター', path: '/development/posters/' },
    ];

    for (const { name, path: expectedPath } of links) {
      await page.goto('/development/index.html');
      await page.waitForFunction(
        () => !document.body.classList.contains('is-loading'),
        null,
        { timeout: 5000 }
      ).catch(() => {});
      const link = page.locator('.portal-catalog a').filter({ hasText: name }).first();
      await expect(link).toBeVisible();
      await link.click();
      await expect(page).toHaveURL(new RegExp(expectedPath.replace(/\//g, '\\/') + '(index\\.html)?$'));
    }
  });

  test('アプリ開発一覧から各プロジェクトへ遷移できる', async ({ page }) => {
    await page.goto('/development/app/');
    const projects = [
      { text: 'FDS体験アプリ', path: /\/development\/app\/fds\// },
      { text: 'VR体験', path: /\/development\/vr\// },
      { text: '消火訓練アプリ', path: /\/development\/app\/firefight\// },
      { text: 'NanoVDB', path: /\/development\/app\/nvdb\// },
    ];
    for (const { text, path: urlPattern } of projects) {
      await page.goto('/development/app/');
      await page.locator('.portal-catalog a').filter({ hasText: text }).first().click();
      await expect(page).toHaveURL(urlPattern);
      expect((await page.title()).length).toBeGreaterThan(0);
    }
  });
});
