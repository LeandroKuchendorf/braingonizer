/**
 * Settings Page JavaScript
 * Handles debug toggle switches and AJAX updates
 */

document.addEventListener('DOMContentLoaded', () => {
    debugLog('Settings page loaded.', 'settings');

    const globalToggle = document.getElementById('debug-global-toggle');
    const pageToggles = document.querySelectorAll('.page-debug-toggle');

    function setPageTogglesLocked(locked) {
        pageToggles.forEach(toggle => {
            toggle.disabled = locked;
            if (locked) {
                toggle.closest('div[style]').style.opacity = '0.45';
                toggle.closest('div[style]').style.cursor = 'not-allowed';
                toggle.style.cursor = 'not-allowed';
            } else {
                toggle.closest('div[style]').style.opacity = '1';
                toggle.closest('div[style]').style.cursor = '';
                toggle.style.cursor = 'pointer';
            }
        });
    }

    // Apply locked state on page load if global is already ON
    if (globalToggle && globalToggle.checked) {
        setPageTogglesLocked(true);
    }

    // Handle global debug toggle
    if (globalToggle) {
        globalToggle.addEventListener('change', async function() {
            const isEnabled = this.checked;
            const value = isEnabled ? '1' : '0';

            debugLog(`Global debug toggle changed to: ${value}`, 'settings');

            try {
                const response = await axios.post('/braingonizer/settings.php', new URLSearchParams({
                    action: 'update_global',
                    value: value
                }));

                debugResponse(response.data, 'settings');

                if (response.data.success) {
                    // Lock or unlock per-page toggles based on global state
                    setPageTogglesLocked(isEnabled);

                    // Update all page toggles to match global state
                    pageToggles.forEach(toggle => {
                        toggle.checked = isEnabled;
                    });

                    // Update the global DEBUG_SETTINGS object
                    window.DEBUG_SETTINGS.debug_global = value;
                    pageToggles.forEach(toggle => {
                        const page = toggle.dataset.page;
                        window.DEBUG_SETTINGS['debug_' + page] = value;
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Revert toggle on failure
                    this.checked = !isEnabled;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message || 'Failed to update global debug setting'
                    });
                }
            } catch (error) {
                debugLog(`Error updating global debug: ${error}`, 'settings');
                
                // Revert toggle on error
                this.checked = !isEnabled;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the setting'
                });
            }
        });
    }

    // Handle individual page debug toggles
    pageToggles.forEach(toggle => {
        toggle.addEventListener('change', async function() {
            const page = this.dataset.page;
            const isEnabled = this.checked;
            const value = isEnabled ? '1' : '0';
            const settingKey = 'debug_' + page;

            debugLog(`${page} debug toggle changed to: ${value}`, 'settings');

            try {
                const response = await axios.post('/braingonizer/settings.php', new URLSearchParams({
                    action: 'update_single',
                    key: settingKey,
                    value: value
                }));

                debugResponse(response.data, 'settings');

                if (response.data.success) {
                    // Update the global DEBUG_SETTINGS object
                    window.DEBUG_SETTINGS[settingKey] = value;

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${page.charAt(0).toUpperCase() + page.slice(1)} debugging ${isEnabled ? 'enabled' : 'disabled'}`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Revert toggle on failure
                    this.checked = !isEnabled;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message || 'Failed to update debug setting'
                    });
                }
            } catch (error) {
                debugLog(`Error updating ${page} debug: ${error}`, 'settings');
                
                // Revert toggle on error
                this.checked = !isEnabled;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the setting'
                });
            }
        });
    });

});
