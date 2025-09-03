<script>
// Simple approach using event delegation to avoid DOM conflicts
document.addEventListener('DOMContentLoaded', function() {
    console.log('Code details handler loaded');
    
    // Simple modal HTML template
    const modalTemplate = `
        <div id="codeModal" style="
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); z-index: 999999; 
            display: flex; align-items: center; justify-content: center; padding: 20px;
        ">
            <div onclick="event.stopPropagation()" style="
                background: white; border-radius: 8px; max-width: 500px; width: 100%; 
                max-height: 400px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            ">
                <div style="
                    display: flex; justify-content: space-between; align-items: center; 
                    padding: 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;
                ">
                    <h3 id="modalTitle" style="margin: 0; font-size: 18px; font-weight: 600;"></h3>
                    <button onclick="closeCodeModal()" style="
                        background: none; border: none; font-size: 24px; cursor: pointer; 
                        color: #666; padding: 0; width: 30px; height: 30px;
                    ">&times;</button>
                </div>
                <div id="modalContent" style="padding: 16px; max-height: 300px; overflow-y: auto;"></div>
                <div style="
                    display: flex; justify-content: flex-end; padding: 16px; 
                    border-top: 1px solid #e5e7eb; background: #f9fafb;
                ">
                    <button onclick="closeCodeModal()" style="
                        background: #3b82f6; color: white; border: none; padding: 8px 16px; 
                        border-radius: 4px; cursor: pointer;
                    ">Tutup</button>
                </div>
            </div>
        </div>
    `;
    
    // Global functions
    window.showCodeDetails = function(code) {
        console.log('showCodeDetails called:', code);
        
        if (!code) {
            alert('Kode tidak valid');
            return false;
        }
        
        fetch('/admin/get-code-details/' + encodeURIComponent(code))
            .then(function(response) {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    showModal(data.title, data.content);
                } else {
                    alert('Detail tidak ditemukan untuk kode: ' + code);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil detail');
            });
        
        return false;
    };
    
    function showModal(title, content) {
        // Remove existing modal if any
        const existing = document.getElementById('codeModal');
        if (existing) {
            existing.remove();
        }
        
        // Add modal to body
        const modalDiv = document.createElement('div');
        modalDiv.innerHTML = modalTemplate;
        document.body.appendChild(modalDiv.firstElementChild);
        
        // Set content
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalContent').innerHTML = content;
        
        // Close on backdrop click
        document.getElementById('codeModal').onclick = function(e) {
            if (e.target.id === 'codeModal') {
                closeCodeModal();
            }
        };
        
        // Close on escape
        document.addEventListener('keydown', function escapeHandler(e) {
            if (e.key === 'Escape') {
                closeCodeModal();
                document.removeEventListener('keydown', escapeHandler);
            }
        });
    }
    
    window.closeCodeModal = function() {
        const modal = document.getElementById('codeModal');
        if (modal) {
            modal.remove();
        }
    };
    
    console.log('Code details handler ready');
});
</script>
