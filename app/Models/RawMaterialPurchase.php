<?php

namespace App\Models;

use App\Traits\HandlesAccountTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchase extends Model
{
    use HandlesAccountTransactions, HasFactory;

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'account_id',
        'purchase_date',
        'cost_details',
        'total_cost',
        'total_price',
        'net_total',
        'amount',
        'payment_type',
        'status'];

    public function getTransactionType(): string
    {
        if ($this->status === 'approved') {
            return 'out';
        } elseif ($this->status === 'pending' || $this->status === 'rejected') {
            return 'in';
        }
        return 'out';
    }

    public function supplier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function warehouse(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function raw_materials(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'purchase_raw_material')
            ->withPivot( 'raw_material_purchase_id',
                'raw_material_id',
                'brand_id',
                'size_id',
                'color_id',
                'warehouse_id',
                'price',
                'quantity',
                'total_price');
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function accountTransaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AccountTransaction::class, 'model_id')
            ->where('model', '=', get_class($this));
    }
}
