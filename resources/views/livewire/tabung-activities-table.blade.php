<div>
    {{ $this->table }}
</div>

<script>
window.showCodeDetails = function(code) {
    fetch('/admin/get-code-details/' + encodeURIComponent(code))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModal(data.title, data.content);
            } else {
                alert('Detail tidak ditemukan untuk kode: ' + code);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil detail');
        });
};

function showModal(title, content) {
    // Remove any existing modal
    const existingModal = document.querySelector('.code-details-modal');
    if (existingModal) {
        document.body.removeChild(existingModal);
    }
    
    const backdrop = document.createElement('div');
    backdrop.className = 'code-details-modal fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    backdrop.onclick = function(e) {
        if (e.target === backdrop) {
            document.body.removeChild(backdrop);
        }
    };
    
    const modal = document.createElement('div');
    modal.className = 'bg-white rounded-lg shadow-xl max-w-lg w-full max-h-96 overflow-hidden';
    modal.innerHTML = `
        <div class="flex items-center justify-between p-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
            <button onclick="document.body.removeChild(this.closest('.code-details-modal'))" 
                    class="text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none">×</button>
        </div>
        <div class="p-4 overflow-y-auto max-h-80">
            ${content}
        </div>
        <div class="flex justify-end p-4 border-t bg-gray-50">
            <button onclick="document.body.removeChild(this.closest('.code-details-modal'))" 
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                Tutup
            </button>
        </div>
    `;
    
    backdrop.appendChild(modal);
    document.body.appendChild(backdrop);
}
</script>
