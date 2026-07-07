document.addEventListener("DOMContentLoaded", () => {
    const timeElement = document.getElementById("currentTime");
    const dateElement = document.getElementById("currentDate");
    const searchForm = document.getElementById("navSearchForm");
    const searchInput = document.getElementById("searchInput");
    const searchToggle = document.getElementById("searchEngineToggle");
    const searchDropdown = document.getElementById("searchEngines");
    const searchIcon = document.getElementById("searchIcon");
    const tabButtons = document.getElementById("tabButtons");
    const aiChatTrigger = document.getElementById("aiChatTrigger");
    const aiChatModal = document.getElementById("aiChatModal");
    const aiChatContainer = document.getElementById("aiChatContainer");
    const aiChatFrame = document.getElementById("aiChatFrame");
    const aiChatClose = document.getElementById("aiChatClose");
    const aiChatMaximize = document.getElementById("aiChatMaximize");
    const aiChatHeader = document.querySelector(".ai-chat-header");
    const aiChatResizeHandle = document.querySelector(".ai-chat-resize-handle");
    let currentSearchUrl = searchInput ? searchInput.dataset.defaultUrl : "https://www.baidu.com/s?wd=";

    function updateClock() {
        const now = new Date();
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString("zh-CN", { hour12: false });
        }
        if (dateElement) {
            dateElement.textContent = now.toLocaleDateString("zh-CN", {
                year: "numeric",
                month: "long",
                day: "numeric",
                weekday: "long"
            });
        }
    }

    updateClock();
    window.setInterval(updateClock, 1000);

    function setSearchEngine(option) {
        if (!option || !searchInput) {
            return;
        }

        currentSearchUrl = option.dataset.url || currentSearchUrl;
        searchInput.placeholder = option.dataset.placeholder || "搜索...";

        if (searchIcon && option.dataset.icon) {
            searchIcon.innerHTML = option.dataset.icon;
        }

        document.querySelectorAll(".search-engine-option").forEach((item) => {
            item.classList.toggle("active", item === option);
        });
        window.localStorage.setItem("navtabsSearchUrl", currentSearchUrl);
    }

    const savedSearchUrl = window.localStorage.getItem("navtabsSearchUrl");
    const searchOptions = Array.from(document.querySelectorAll(".search-engine-option"));
    const savedOption = searchOptions.find((option) => option.dataset.url === savedSearchUrl);
    setSearchEngine(savedOption || searchOptions[0]);

    if (searchToggle && searchDropdown) {
        searchToggle.addEventListener("click", (event) => {
            event.preventDefault();
            event.stopPropagation();
            searchDropdown.classList.toggle("show");
            searchToggle.classList.toggle("active");
        });

        searchDropdown.addEventListener("click", (event) => {
            const option = event.target.closest(".search-engine-option");
            if (!option) {
                return;
            }
            setSearchEngine(option);
            searchDropdown.classList.remove("show");
            searchToggle.classList.remove("active");
            searchInput.focus();
        });

        document.addEventListener("click", (event) => {
            if (!event.target.closest(".search-container")) {
                searchDropdown.classList.remove("show");
                searchToggle.classList.remove("active");
            }
        });
    }

    if (searchForm && searchInput) {
        searchForm.addEventListener("submit", (event) => {
            event.preventDefault();
            const query = searchInput.value.trim();
            if (!query) {
                searchInput.focus();
                return;
            }
            window.open(currentSearchUrl + encodeURIComponent(query), "_blank", "noopener,noreferrer");
        });

    }

    if (tabButtons) {
        const buttons = Array.from(tabButtons.querySelectorAll(".tab-button"));
        const panels = Array.from(document.querySelectorAll("[data-panel]"));

        function activateTab(targetId) {
            const targetPanel = panels.find((panel) => panel.id === targetId) || panels[0];
            if (!targetPanel) {
                return;
            }

            buttons.forEach((button) => {
                button.classList.toggle("active", button.dataset.target === targetPanel.id);
            });
            panels.forEach((panel) => {
                panel.classList.toggle("active", panel === targetPanel);
            });
            window.localStorage.setItem("navtabsActiveGroup", targetPanel.id);
        }

        tabButtons.addEventListener("click", (event) => {
            const button = event.target.closest(".tab-button");
            if (!button) {
                return;
            }
            activateTab(button.dataset.target);
        });

        activateTab(window.localStorage.getItem("navtabsActiveGroup") || (buttons[0] && buttons[0].dataset.target));
    }

    document.addEventListener("keydown", (event) => {
        if (event.key === "/" && document.activeElement !== searchInput) {
            event.preventDefault();
            searchInput && searchInput.focus();
        }
    });

    if (aiChatTrigger && aiChatModal && aiChatContainer && aiChatFrame) {
        let isMaximized = false;
        let isDragging = false;
        let isResizing = false;
        let dragOffset = { x: 0, y: 0 };
        let resizeStart = { width: 0, height: 0, x: 0, y: 0 };

        function openAiChat() {
            aiChatFrame.src = aiChatFrame.dataset.src || "about:blank";
            aiChatModal.classList.add("show");
            aiChatModal.setAttribute("aria-hidden", "false");
        }

        function closeAiChat() {
            aiChatModal.classList.remove("show");
            aiChatModal.setAttribute("aria-hidden", "true");
            isDragging = false;
            isResizing = false;
            window.setTimeout(() => {
                if (!aiChatModal.classList.contains("show")) {
                    aiChatFrame.src = "about:blank";
                }
            }, 250);
        }

        function restoreAiWindow() {
            aiChatContainer.classList.remove("maximized");
            isMaximized = false;
            if (aiChatMaximize) {
                aiChatMaximize.textContent = "□";
                aiChatMaximize.title = "最大化";
                aiChatMaximize.setAttribute("aria-label", "最大化");
            }
        }

        function maximizeAiWindow() {
            aiChatContainer.classList.add("maximized");
            isMaximized = true;
            if (aiChatMaximize) {
                aiChatMaximize.textContent = "↙";
                aiChatMaximize.title = "还原";
                aiChatMaximize.setAttribute("aria-label", "还原");
            }
        }

        function onPointerMove(event) {
            if (isDragging && !isMaximized) {
                const width = aiChatContainer.offsetWidth;
                const height = aiChatContainer.offsetHeight;
                const x = Math.max(0, Math.min(event.clientX - dragOffset.x, window.innerWidth - width));
                const y = Math.max(0, Math.min(event.clientY - dragOffset.y, window.innerHeight - height));
                aiChatContainer.style.left = `${x}px`;
                aiChatContainer.style.top = `${y}px`;
                aiChatContainer.style.right = "auto";
                aiChatContainer.style.bottom = "auto";
                aiChatContainer.style.transform = "none";
            }

            if (isResizing && !isMaximized) {
                const nextWidth = Math.min(window.innerWidth * 0.9, Math.max(300, resizeStart.width + event.clientX - resizeStart.x));
                const nextHeight = Math.min(window.innerHeight * 0.9, Math.max(400, resizeStart.height + event.clientY - resizeStart.y));
                aiChatContainer.style.width = `${nextWidth}px`;
                aiChatContainer.style.height = `${nextHeight}px`;
            }
        }

        function stopPointerAction() {
            isDragging = false;
            isResizing = false;
            document.body.style.userSelect = "";
            document.body.style.cursor = "";
            document.removeEventListener("mousemove", onPointerMove);
            document.removeEventListener("mouseup", stopPointerAction);
        }

        aiChatTrigger.addEventListener("click", openAiChat);
        aiChatClose && aiChatClose.addEventListener("click", closeAiChat);
        aiChatModal.addEventListener("click", (event) => {
            if (event.target === aiChatModal) {
                closeAiChat();
            }
        });

        aiChatMaximize && aiChatMaximize.addEventListener("click", () => {
            if (isMaximized) {
                restoreAiWindow();
            } else {
                maximizeAiWindow();
            }
        });

        aiChatHeader && aiChatHeader.addEventListener("mousedown", (event) => {
            if (isMaximized || event.target.closest(".ai-chat-controls")) {
                return;
            }
            const rect = aiChatContainer.getBoundingClientRect();
            isDragging = true;
            dragOffset = {
                x: event.clientX - rect.left,
                y: event.clientY - rect.top
            };
            document.body.style.userSelect = "none";
            document.addEventListener("mousemove", onPointerMove);
            document.addEventListener("mouseup", stopPointerAction);
        });

        aiChatResizeHandle && aiChatResizeHandle.addEventListener("mousedown", (event) => {
            if (isMaximized) {
                return;
            }
            event.preventDefault();
            const rect = aiChatContainer.getBoundingClientRect();
            isResizing = true;
            resizeStart = {
                width: rect.width,
                height: rect.height,
                x: event.clientX,
                y: event.clientY
            };
            document.body.style.cursor = "se-resize";
            document.body.style.userSelect = "none";
            document.addEventListener("mousemove", onPointerMove);
            document.addEventListener("mouseup", stopPointerAction);
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && aiChatModal.classList.contains("show")) {
                closeAiChat();
            }
        });
    }
});
