function updateBodyScroll() {
    const anyModalOpen = document.querySelectorAll('.modal:not(.hidden)').length > 0;
    document.body.style.overflow = anyModalOpen ? 'hidden' : 'auto';
}


