<!-- Multiple Upload Modal -->
<div class="modal" id="multipleUploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-3 border-dark" style="box-shadow: 8px 8px 0 #000;">
            <form id="multipleUploadForm" action="{{ route('tickets.documents.upload-multiple', $ticket) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white border-bottom border-3 border-dark">
                    <h5 class="modal-title">Upload Multiple Files</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="document_type" id="multiple_document_type">
                    <h6 class="fw-bold mb-3" id="multiple_document_name"></h6>
                    <div class="alert alert-info py-2">
                        <small><i class="bi bi-info-circle"></i> You can upload up to 10 files at once (PDF, Images, Documents)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Files <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="files[]" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" multiple required>
                        <small class="text-muted">Accepted: PDF, PNG, JPG, DOC, DOCX (Max 10MB each)</small>
                    </div>
                </div>
                <div class="modal-footer border-top border-3 border-dark">
                    <button type="button" class="btn btn-white border border-2 border-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary border border-2 border-dark" style="box-shadow: 2px 2px 0 #000;">
                        <i class="bi bi-cloud-upload"></i> Upload Files
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
