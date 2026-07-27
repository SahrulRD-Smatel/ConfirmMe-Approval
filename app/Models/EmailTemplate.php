<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'workflow',
        'target',
        'subject',
        'greeting',
        'body',
        'to_config',
        'cc_config',
        'is_active',
        'description',
    ];

    protected $casts = [
        'to_config' => 'array',
        'cc_config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get template by code
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get template for specific workflow and target
     */
    public static function findForWorkflow(string $workflow, string $target): ?self
    {
        return static::where('workflow', $workflow)
            ->where('target', $target)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all templates for a workflow type
     */
    public static function forWorkflow(string $workflow)
    {
        return static::where('workflow', $workflow)
            ->orWhere('workflow', 'all')
            ->where('is_active', true)
            ->get();
    }

    /**
     * Replace placeholders in subject
     */
    public function getProcessedSubject(array $placeholders): string
    {
        return $this->replacePlaceholders($this->subject, $placeholders);
    }

    /**
     * Replace placeholders in greeting
     */
    public function getProcessedGreeting(array $placeholders): string
    {
        return $this->replacePlaceholders($this->greeting, $placeholders);
    }

    /**
     * Replace placeholders in body
     */
    public function getProcessedBody(array $placeholders): string
    {
        return $this->replacePlaceholders($this->body, $placeholders);
    }

    /**
     * Replace placeholders in text
     */
    protected function replacePlaceholders(string $text, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value ?? '', $text);
        }
        return $text;
    }

    /**
     * Get available placeholder list for documentation
     */
    public static function getAvailablePlaceholders(): array
    {
        return [
            'doc_id' => 'Nomor dokumen',
            'doc_subject' => 'Judul dokumen',
            'staff_name' => 'Nama pemohon/staff',
            'staff_email' => 'Email pemohon',
            'next_approver_name' => 'Nama approver berikutnya',
            'next_approver_email' => 'Email approver berikutnya',
            'prev_approver_name' => 'Nama approver sebelumnya',
            'amount' => 'Jumlah/nominal (formatted)',
            'vendor_name' => 'Nama vendor',
            'rejected_by_name' => 'Nama yang menolak',
            'reason' => 'Alasan penolakan',
            'company_name' => 'Nama perusahaan',
            'app_url' => 'URL aplikasi',
        ];
    }
}
