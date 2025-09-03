<x-filament-panels::page>
    {{ $this->table }}
    
    <script>
    window.showCodeDetails = function(code) {
        if (!code) return false;
        
        fetch('/admin/get-code-details/' + encodeURIComponent(code))
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    // Remove existing modal
                    var existing = document.getElementById('codeModal');
                    if (existing) existing.remove();
                    
                    // Create new modal
                    var modal = document.createElement('div');
                    modal.id = 'codeModal';
                    modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999999;display:flex;align-items:center;justify-content:center;padding:20px;';
                    
                    var content = document.createElement('div');
                    content.style.cssText = 'background:white;border-radius:8px;max-width:500px;width:100%;max-height:400px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.3);';
                    
                    content.innerHTML = 
                        '<div style="display:flex;justify-content:space-between;align-items:center;padding:16px;border-bottom:1px solid #e5e7eb;background:#f9fafb;">' +
                            '<h3 style="margin:0;font-size:18px;font-weight:600;">' + data.title + '</h3>' +
                            '<button onclick="closeCodeModal()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#666;">&times;</button>' +
                        '</div>' +
                        '<div style="padding:16px;max-height:300px;overflow-y:auto;">' + data.content + '</div>' +
                        '<div style="display:flex;justify-content:flex-end;padding:16px;border-top:1px solid #e5e7eb;background:#f9fafb;">' +
                            '<button onclick="closeCodeModal()" style="background:#3b82f6;color:white;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;">Tutup</button>' +
                        '</div>';
                    
                    modal.appendChild(content);
                    modal.onclick = function(e) { if (e.target === modal) closeCodeModal(); };
                    document.body.appendChild(modal);
                } else {
                    alert('Detail tidak ditemukan untuk kode: ' + code);
                }
            })
            .catch(function() { 
                alert('Terjadi kesalahan saat mengambil detail'); 
            });
        
        return false;
    };
    
    window.closeCodeModal = function() {
        var modal = document.getElementById('codeModal');
        if (modal) modal.remove();
    };
    </script>
</x-filament-panels::page>
