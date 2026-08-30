document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("globalModal");
    if (!modal) return;

    const modalContent = document.getElementById("modalContent");
    const closeModal = () => {
        modal.classList.remove("open");
        document.body.classList.remove("modal-open");
        modalContent.innerHTML = "";
    };

    const renderModalForm = (title, formHtml) => {
        modalContent.innerHTML = `
            <div class="modal-header">
                <h3>${title}</h3>
            </div>
            ${formHtml}
        `;

        const modalForm = modalContent.querySelector("form");
        if (modalForm) {
            modalForm.classList.add("modal-form");
        }

        modal.classList.add("open");
        document.body.classList.add("modal-open");
    };

    const openModalFromUrl = async (url, title = "Form") => {
        try {
            const response = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!response.ok) throw new Error("Failed to load form");
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const form = doc.querySelector("form");
            const formHtml = form ? form.outerHTML : html;

            renderModalForm(title, formHtml);
        } catch (error) {
            renderModalForm(
                title,
                '<div class="alert alert-error">Gagal memuat form. Silakan coba lagi.</div>',
            );
        }
    };

    const openDeleteModal = (url, title) => {
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "";

        renderModalForm(
            title,
            `
                <form action="${url}" method="POST" class="modal-form">
                    <input type="hidden" name="_token" value="${csrfToken}" />
                    <input type="hidden" name="_method" value="DELETE" />
                    <p class="delete-confirm-text">Apakah Anda yakin ingin menghapus data ini?</p>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary modal-close-btn">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            `,
        );

        const closeButton = modalContent.querySelector(".modal-close-btn");
        if (closeButton) {
            closeButton.addEventListener("click", closeModal);
        }
    };

    document.querySelectorAll("[data-modal-open]").forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            const url = this.dataset.modalOpen || "#";
            const title = this.dataset.modalTitle || "Form";
            openModalFromUrl(url, title);
        });
    });

    document.querySelectorAll("[data-modal-target]").forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            const url = this.dataset.modalTarget || "#";
            const title = this.dataset.modalTitle || "Form";
            openModalFromUrl(url, title);
        });
    });

    document.querySelectorAll("[data-delete-url]").forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            const url = this.dataset.deleteUrl || "#";
            const title = this.dataset.deleteTitle || "Hapus Data";
            openDeleteModal(url, title);
        });
    });

    document.querySelectorAll(".modal-close").forEach((button) => {
        button.addEventListener("click", closeModal);
    });

    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && modal.classList.contains("open")) {
            closeModal();
        }
    });
});
