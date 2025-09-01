<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userNameToDelete"></strong> ?</p>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Cette action est irréversible !
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUser">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de changement de statut -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-toggle-on me-2"></i>
                    Changer le statut
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="statusChangeMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmToggleStatus">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression
    const deleteButtons = document.querySelectorAll('.delete-user-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    const userNameElement = document.getElementById('userNameToDelete');
    const confirmDeleteBtn = document.getElementById('confirmDeleteUser');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            userNameElement.textContent = userName;
            confirmDeleteBtn.onclick = function() {
                deleteUser(userId);
            };
            
            deleteModal.show();
        });
    });
    
    // Gestion du changement de statut
    const toggleButtons = document.querySelectorAll('.toggle-status-btn');
    const toggleModal = new bootstrap.Modal(document.getElementById('toggleStatusModal'));
    const statusMessageElement = document.getElementById('statusChangeMessage');
    const confirmToggleBtn = document.getElementById('confirmToggleStatus');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const currentStatus = this.dataset.currentStatus === '1';
            const action = currentStatus ? 'désactiver' : 'activer';
            
            statusMessageElement.textContent = `Voulez-vous vraiment ${action} l'utilisateur "${userName}" ?`;
            confirmToggleBtn.onclick = function() {
                toggleUserStatus(userId);
            };
            
            toggleModal.show();
        });
    });
});

function deleteUser(userId) {
    fetch(`/admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue.');
    });
}

function toggleUserStatus(userId) {
    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue.');
    });
}
</script>
