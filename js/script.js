function toggleMobileMenu() {
  const menu = document.getElementById("mobileMenu");
  const backButton = document.getElementById("backButton");
  menu.classList.toggle("open");
  backButton.classList.toggle("hidden");
}

// Auto-focus no primeiro campo
document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("username").focus();
});

// Mostrar notificação
function showNotification(message, type = "info") {
  // Remover notificações existentes
  const existingNotifications = document.querySelectorAll(".notification");
  existingNotifications.forEach((notification) => notification.remove());

  // Criar nova notificação
  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
  notification.innerHTML = `
        <i class="fas fa-${
          type === "success"
            ? "check-circle"
            : type === "error"
            ? "exclamation-triangle"
            : "info-circle"
        } mr-2"></i>
        ${message}
    `;

  document.body.appendChild(notification);

  // Remoção automática após 5 segundos
  setTimeout(() => {
    notification.remove();
  }, 5000);
}

// Rolagem suave para o elemento
function scrollToElement(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }
}

// ===== FUNÇÕES DE PESQUISA E FILTRO =====

// Filtrar fichas em tempo real
function filterFichas() {
  const searchTerm = document
    .getElementById("searchFichas")
    .value.toLowerCase();
  const rows = document.querySelectorAll(".ficha-row");

  rows.forEach((row) => {
    const name = row.querySelector(".ficha-name").textContent.toLowerCase();
    if (name.includes(searchTerm)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// Funcionalidade de pesquisa avançada
function performAdvancedSearch() {
  const searchForm = document.getElementById("advancedSearchForm");
  if (searchForm) {
    const formData = new FormData(searchForm);
    const params = new URLSearchParams();

    for (let [key, value] of formData.entries()) {
      if (value.trim() !== "") {
        params.append(key, value);
      }
    }

    window.location.href = "fichas.php?" + params.toString();
  }
}

// Limpar todos os filtros
function clearFilters() {
  const form = document.querySelector('form[method="GET"]');
  if (form) {
    const inputs = form.querySelectorAll("input, select");
    inputs.forEach((input) => {
      if (input.type === "text" || input.type === "search") {
        input.value = "";
      } else if (input.tagName === "SELECT") {
        input.selectedIndex = 0;
      }
    });

    // Redirecionar para URL limpa
    window.location.href = window.location.pathname;
  }
}

// ===== FUNÇÕES MODAIS =====

// Mostrar modal
function showModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.style.overflow = "hidden";
  }
}

// Ocultar modal
function hideModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    document.body.style.overflow = "auto";
  }
}

// Fechar modal ao clicar fora
function setupModalCloseOnOutsideClick(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.addEventListener("click", function (e) {
      if (e.target === modal) {
        hideModal(modalId);
      }
    });
  }
}

// ===== FUNÇÕES DE FORMULÁRIO =====

// Formulário de envio automático em alterações selecionadas
function setupAutoSubmitOnChange(selectSelector) {
  const selects = document.querySelectorAll(selectSelector);
  selects.forEach((select) => {
    select.addEventListener("change", function () {
      this.form.submit();
    });
  });
}

// Validação de formulário
function validateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return false;

  const requiredFields = form.querySelectorAll("[required]");
  let isValid = true;

  requiredFields.forEach((field) => {
    if (!field.value.trim()) {
      field.classList.add("border-red-500");
      isValid = false;
    } else {
      field.classList.remove("border-red-500");
    }
  });

  return isValid;
}

// ===== FUNÇÕES DE ANIMAÇÃO =====

// Animar elementos na rolagem
function animateOnScroll() {
  const elements = document.querySelectorAll(".animate-on-scroll");

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("fade-in");
      }
    });
  });

  elements.forEach((element) => {
    observer.observe(element);
  });
}

// Adicionar animação de carregamento aos botões
function addLoadingToButton(buttonId, loadingText = "Carregando...") {
  const button = document.getElementById(buttonId);
  if (button) {
    const originalText = button.innerHTML;
    button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${loadingText}`;
    button.disabled = true;

    return function () {
      button.innerHTML = originalText;
      button.disabled = false;
    };
  }
}

// ===== INICIALIZAÇÃO =====

// Inicializar todas as funções quando o DOM for carregado
document.addEventListener("DOMContentLoaded", function () {
  // Configurar envio automático para seleções de filtros
  setupAutoSubmitOnChange(
    'select[name="categoria"], select[name="montadoras"]'
  );

  // Configurar fechamento modal no clique externo
  setupModalCloseOnOutsideClick("deleteModal");

  // Configurar rolagem suave para links de âncora
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href").substring(1);
      scrollToElement(targetId);
    });
  });

  // Configurar entrada de pesquisa com debounce
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        const activeFilter = document.querySelector(".filter-btn.active");
        const brand = activeFilter ? activeFilter.textContent.trim() : "all";
        // Isso acionaria uma função de pesquisa se implementada
      }, 300);
    });
  }

  // Foco automático na primeira entrada em formulários
  const firstInput = document.querySelector(
    'form input[type="text"], form input[type="email"]'
  );
  if (firstInput) {
    firstInput.focus();
  }

  // Configurar animação na rolagem
  animateOnScroll();

  // Mostrar mensagens de sucesso/erro dos parâmetros de URL
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("success")) {
    showNotification(urlParams.get("success"), "success");
  }
  if (urlParams.get("error")) {
    showNotification(urlParams.get("error"), "error");
  }

  // Rola para o topo quando a página carregar (para páginas de detalhes)
  if (window.location.pathname.includes("detalhes.php")) {
    window.scrollTo(0, 0);
  }
});

// ===== ATALHOS DE TECLADO =====

// Configurar atalhos de teclado
document.addEventListener("keydown", function (e) {
  // Ctrl/Cmd + K para pesquisa
  if ((e.ctrlKey || e.metaKey) && e.key === "k") {
    e.preventDefault();
    const searchInput =
      document.getElementById("searchInput") ||
      document.getElementById("searchFichas");
    if (searchInput) {
      searchInput.focus();
    }
  }

  // Escape para fechar modais
  if (e.key === "Escape") {
    const visibleModal = document.querySelector(".fixed:not(.hidden)");
    if (visibleModal && visibleModal.id) {
      hideModal(visibleModal.id);
    }
  }
});

// ===== FUNÇÕES DE EXPORTAÇÃO =====

// Funcionalidade de exportação de dados
function exportData(format = "json") {
  const loadingButton = addLoadingToButton("exportBtn", "Exportando...");

  // Simular processo de exportação
  setTimeout(() => {
    showNotification("Exportação concluída!", "success");
    if (loadingButton) loadingButton();
  }, 2000);
}

// ===== AULAS DE UTILIDADE =====

// Adicionar classes de utilitários dinamicamente
function addUtilityClasses() {
  const style = document.createElement("style");
  style.textContent = `
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        
        .animate-on-scroll.fade-in {
            opacity: 1;
            transform: translateY(0);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    `;
  document.head.appendChild(style);
}

document.addEventListener("DOMContentLoaded", function () {
  const track = document.getElementById("carouselTrack");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const items = track.querySelectorAll(".carousel-item");

  let scrollIndex = 0;
  let visibleCount = 1;
  let isDragging = false;
  let startPos = 0;
  let currentTranslate = 0;
  let prevTranslate = 0;

  function updateVisibleCount() {
    const width = window.innerWidth;
    if (width >= 1024) visibleCount = 5;
    else if (width >= 640) visibleCount = 3;
    else visibleCount = 2;
  }

  function updateCarousel(translate = null) {
    const itemWidth = items[0].getBoundingClientRect().width + 16;
    const scrollAmount =
      translate !== null ? translate : scrollIndex * itemWidth;
    track.style.transform = `translateX(-${scrollAmount}px)`;
    prevBtn.style.display = scrollIndex > 0 ? "block" : "none";
    nextBtn.style.display =
      scrollIndex + visibleCount < items.length ? "block" : "none";
  }

  // Eventos de toque
  track.addEventListener("touchstart", touchStart);
  track.addEventListener("touchmove", touchMove);
  track.addEventListener("touchend", touchEnd);

  // Eventos de mouse
  track.addEventListener("mousedown", touchStart);
  track.addEventListener("mousemove", touchMove);
  track.addEventListener("mouseup", touchEnd);
  track.addEventListener("mouseleave", touchEnd);

  function touchStart(event) {
    isDragging = true;
    startPos = getPositionX(event);
    track.style.cursor = "grabbing";
    track.style.transition = "none";
  }

  function touchMove(event) {
    if (!isDragging) return;

    const currentPosition = getPositionX(event);
    currentTranslate = prevTranslate + currentPosition - startPos;
    track.style.transform = `translateX(${currentTranslate}px)`;
  }

  function touchEnd() {
    isDragging = false;
    track.style.cursor = "grab";
    track.style.transition = "transform 0.3s ease-in-out";

    const itemWidth = items[0].getBoundingClientRect().width + 16;
    const moveBy = currentTranslate - prevTranslate;

    if (Math.abs(moveBy) > itemWidth / 3) {
      if (moveBy < 0 && scrollIndex + visibleCount < items.length) {
        scrollIndex++;
      } else if (moveBy > 0 && scrollIndex > 0) {
        scrollIndex--;
      }
    }

    updateCarousel();
    prevTranslate = scrollIndex * -itemWidth;
    currentTranslate = prevTranslate;
  }

  function getPositionX(event) {
    return event.type.includes("mouse")
      ? event.pageX
      : event.touches[0].clientX;
  }

  nextBtn.addEventListener("click", () => {
    if (scrollIndex + visibleCount < items.length) {
      scrollIndex++;
      updateCarousel();
    }
  });

  prevBtn.addEventListener("click", () => {
    if (scrollIndex > 0) {
      scrollIndex--;
      updateCarousel();
    }
  });

  window.addEventListener("resize", () => {
    updateVisibleCount();
    updateCarousel();
  });

  updateVisibleCount();
  updateCarousel();
});

function toggleFiltros() {
  const filtros = document.getElementById("filtrosAvancados");
  filtros.classList.toggle("hidden");
}

// Inicializar classes utilitárias
addUtilityClasses();
