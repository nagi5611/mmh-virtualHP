// public_html/airport/dev/assets/admin-tabs.js — お知らせ / 受賞歴タブ切替

(function () {
    "use strict";

    let activeTab = "news";

    function setActiveTab(tab) {
        activeTab = tab === "awards" ? "awards" : "news";

        document.querySelectorAll(".app-tab").forEach((btn) => {
            const isActive = btn.getAttribute("data-tab") === activeTab;
            btn.classList.toggle("is-active", isActive);
            btn.setAttribute("aria-selected", isActive ? "true" : "false");
        });

        const newsPanel = document.getElementById("panel-news");
        const awardsPanel = document.getElementById("panel-awards");
        if (newsPanel) newsPanel.hidden = activeTab !== "news";
        if (awardsPanel) awardsPanel.hidden = activeTab !== "awards";

        const refreshBtn = document.getElementById("refreshBtn");
        if (refreshBtn) {
            refreshBtn.title = activeTab === "awards" ? "受賞歴を再読み込み" : "お知らせを再読み込み";
            refreshBtn.setAttribute(
                "aria-label",
                activeTab === "awards" ? "受賞歴を再読み込み" : "お知らせを再読み込み"
            );
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".app-tab").forEach((btn) => {
            btn.addEventListener("click", () => {
                const tab = btn.getAttribute("data-tab");
                if (!tab) return;
                setActiveTab(tab);
            });
        });

        const refreshBtn = document.getElementById("refreshBtn");
        refreshBtn?.addEventListener("click", () => {
            if (activeTab === "awards" && typeof window.AwardsAdmin?.loadAwards === "function") {
                void window.AwardsAdmin.loadAwards();
                return;
            }
            if (typeof window.NewsAdmin?.loadNews === "function") {
                void window.NewsAdmin.loadNews();
            }
        });

        setActiveTab("news");
    });
})();
