<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TelegramUser
 *
 * @property int $id
 * @property int|null $user_id
 * @property bool $is_bot
 * @property string|null $first_name
 * @property string|null $username
 * @property string|null $language_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chat> $chats
 * @property-read int|null $chats_count
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereIsBot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramUser withoutTrashed()
 * @mixin \Eloquent
 */
class TelegramUser extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'is_bot', 'first_name', 'username', 'language_code'
    ];

    protected $casts = [
        'is_bot' => 'boolean'
    ];

    /**
     * @return HasMany
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'telegram_user_id');
    }

    /**
     * @param array $attributes
     * @return TelegramUser|Model
     */
    public static function makeNew(array $attributes)
    {

        $data = collect($attributes['from'])->only([
            'is_bot', 'first_name', 'username', 'language_code'
        ])->merge([
            'user_id' => $attributes['from']['id'] ?? null
        ])->toArray();

        $resource = self::updateOrCreate(['user_id' => $data['user_id']], $data);
        $resource->saveRelations($attributes);

        return $resource;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function saveRelations(array $attributes): static
    {
        $this->chats()->updateOrCreate(
            ['chat_id' => $attributes['chat']['id']],
            collect($attributes['chat'])->only([
                'first_name', 'username', 'type'
            ])->merge([
                'chat_id' => $attributes['chat']['id'] ?? null
            ])->toArray()
        );
        return $this;
    }
}
