<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TicketDocument extends Model
{


    protected $fillable = [
        'ticket_id',
        'document_type',
        'input_method',
        'allow_multiple',
        'parent_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_by',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function formData()
    {
        return $this->hasOne(TicketDocumentForm::class);
    }

    public function parent()
    {
        return $this->belongsTo(TicketDocument::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TicketDocument::class, 'parent_id');
    }

    // Enhanced method with metadata
    public static function getDocumentTypesWithMeta()
    {
        return [
            'mandatory' => [
                'request_form' => [
                    'name' => 'Formulir Permintaan',
                    'input_methods' => ['form', 'upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf', 'doc', 'docx'],
                    'has_template' => true,
                    'form_fields' => [
                        'tanggal_pengajuan' => ['type' => 'date', 'label' => 'Tanggal Pengajuan', 'required' => true],
                        'nama_pemohon' => ['type' => 'text', 'label' => 'Nama Pemohon', 'required' => true],
                        'unit_organisasi' => ['type' => 'text', 'label' => 'Unit/Organisasi', 'required' => true],
                        'jenis_permintaan' => ['type' => 'select', 'label' => 'Jenis Permintaan', 'required' => true, 'options' => ['New Feature', 'Update', 'Bug Fix', 'Enhancement']],
                        'deskripsi_kebutuhan' => ['type' => 'textarea', 'label' => 'Deskripsi Kebutuhan', 'required' => true],
                        'tujuan' => ['type' => 'textarea', 'label' => 'Tujuan', 'required' => true],
                        'ruang_lingkup' => ['type' => 'textarea', 'label' => 'Ruang Lingkup', 'required' => true],
                        'alasan' => ['type' => 'textarea', 'label' => 'Alasan Permintaan', 'required' => true],
                    ]
                ],
                'user_requirements' => [
                    'name' => 'User Requirements Document',
                    'input_methods' => ['form', 'upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf', 'doc', 'docx'],
                    'has_template' => true,
                    'form_fields' => [
                        'functional_requirements' => ['type' => 'textarea', 'label' => 'Kebutuhan Fungsional', 'required' => true],
                        'non_functional_requirements' => ['type' => 'textarea', 'label' => 'Kebutuhan Non-Fungsional', 'required' => true],
                        'user_stories' => ['type' => 'textarea', 'label' => 'User Stories', 'required' => false],
                        'workflow' => ['type' => 'textarea', 'label' => 'Alur Kerja', 'required' => true],
                        'acceptance_criteria' => ['type' => 'textarea', 'label' => 'Kriteria Penerimaan', 'required' => true],
                    ]
                ],
                'functional_spec' => [
                    'name' => 'Spesifikasi Fungsional, Teknis dan Assets',
                    'input_methods' => ['upload'],
                    'allow_multiple' => true,
                    'accepted_formats' => ['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx'],
                    'has_template' => true,
                ],
                'project_plan' => [
                    'name' => 'Project Plan & Jadwal',
                    'input_methods' => ['auto'],
                    'allow_multiple' => false,
                    'has_template' => false,
                    'integrated_with' => 'tasks'
                ],
                'user_manual' => [
                    'name' => 'User Manual',
                    'input_methods' => ['upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf'],
                    'has_template' => true,
                ],
                'bast' => [
                    'name' => 'Berita Acara Serah Terima',
                    'input_methods' => ['upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf'],
                    'has_template' => true,
                ],
            ],
            'supporting' => [
                'user_story' => [
                    'name' => 'User Story',
                    'input_methods' => ['upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf', 'doc', 'docx'],
                    'has_template' => false,
                ],
                'requirement_signoff' => [
                    'name' => 'Requirement Sign-Off',
                    'input_methods' => ['upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf'],
                    'has_template' => false,
                ],
                'change_request' => [
                    'name' => 'Change Request Form',
                    'input_methods' => ['upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf', 'doc', 'docx'],
                    'has_template' => false,
                ],
                'uat_report' => [
                    'name' => 'Laporan Pengujian (UAT)',
                    'input_methods' => ['upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf', 'doc', 'docx'],
                    'has_template' => false,
                ],
                'installation_report' => [
                    'name' => 'Laporan Instalasi/Update',
                    'input_methods' => ['upload'],
                    'allow_multiple' => false,
                    'accepted_formats' => ['pdf', 'doc', 'docx'],
                    'has_template' => false,
                ],
            ]
        ];
    }

    // Backward compatible method
    public static function getDocumentTypes()
    {
        $meta = self::getDocumentTypesWithMeta();
        $simple = [];
        
        foreach ($meta as $category => $types) {
            $simple[$category] = [];
            foreach ($types as $key => $config) {
                $simple[$category][$key] = is_array($config) ? $config['name'] : $config;
            }
        }
        
        return $simple;
    }

    public static function getDocumentTypeName($type)
    {
        $types = self::getDocumentTypes();
        $allTypes = array_merge($types['mandatory'], $types['supporting']);
        return $allTypes[$type] ?? $type;
    }

    public function isMandatory()
    {
        $types = self::getDocumentTypes();
        return array_key_exists($this->document_type, $types['mandatory']);
    }

    public function getFileSizeFormatted()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    // Scopes
    public function scopeMandatory($query)
    {
        $types = array_keys(self::getDocumentTypes()['mandatory']);
        return $query->whereIn('document_type', $types);
    }

    public function scopeSupporting($query)
    {
        $types = array_keys(self::getDocumentTypes()['supporting']);
        return $query->whereIn('document_type', $types);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
