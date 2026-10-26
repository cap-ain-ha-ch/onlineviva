// Exam-page restrictions are deterrents only; they cannot secure the whole device.
(function () {
    const examForm = document.getElementById('examForm');
    if (!examForm) return;

    let backAttempts = 0;
    let isSubmitting = false;

    // Block common browser-level copy actions on this exam page.
    ['contextmenu', 'copy', 'cut', 'paste', 'dragstart', 'selectstart'].forEach(function (eventName) {
        document.addEventListener(eventName, function (event) {
            event.preventDefault();
        });
    });

    document.addEventListener('keydown', function (event) {
        const key = (event.key || '').toLowerCase();
        const commandKey = event.ctrlKey || event.metaKey;
        const blockedCommand = commandKey && ['c', 'x', 'v', 'u', 's', 'p'].includes(key);
        const developerShortcut = event.key === 'F12' || (commandKey && event.shiftKey && ['i', 'j', 'c'].includes(key));
        if (blockedCommand || developerShortcut) {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    examForm.addEventListener('submit', function (event) {
        // Automatic submission uses form.submit(), which intentionally bypasses this confirmation.
        if (event.submitter && event.submitter.hasAttribute('data-manual-submit')) {
            const confirmed = window.confirm('Submit your examination now? You cannot attempt this exam again with the same roll number.');
            if (!confirmed) {
                event.preventDefault();
                return;
            }
        }
        isSubmitting = true;
    });

    window.addEventListener('beforeunload', function (event) {
        if (!isSubmitting) {
            event.preventDefault();
            event.returnValue = '';
        }
    });

    // Two-step browser Back handling: warn once, submit on the second attempt.
    try {
        window.history.pushState({ examGuard: true }, '', window.location.href);
        window.addEventListener('popstate', function () {
            if (isSubmitting) return;
            backAttempts += 1;
            if (backAttempts === 1) {
                window.alert('Warning 1 of 2: leaving the exam is restricted. Press Back again and the exam will be submitted.');
                window.history.pushState({ examGuard: true }, '', window.location.href);
                return;
            }
            isSubmitting = true;
            const reason = document.getElementById('submission_reason');
            if (reason) reason.value = 'browser_back';
            window.alert('Second Back attempt detected. Submitting the answers now.');
            examForm.submit();
        });
    } catch (error) {
        // Some browsers restrict history manipulation. The exam remains usable.
    }
})();
