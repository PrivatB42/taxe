
<x-generic.c-modal.content-modal modal-id="modal-logout" size="md" modal-title="Deconnexion">
    <div id="modal-logout-errors" class="mb-3"></div>

    <p class="text-center">Voulez-vous vraiment vous deconnecter ?</p>

    <x-slot name="footer">
        <button type="button" id="closeModal" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <form action="{{ route('auth.logout') }}" method="post">
            @csrf
            <button type="submit" class="btn btn-danger">Se deconnecter</button>
        </form>
    </x-slot>
</x-generic.c-modal.content-modal>
