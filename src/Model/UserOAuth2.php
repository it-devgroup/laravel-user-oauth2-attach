<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;

/**
 * Class UserOAuth2
 * @package ItDevgroup\LaravelUserOAuth2Attach\Model
 * @property-read int $id
 * @property int $user_id
 * @property Model $user
 * @property string $service
 * @property string $external_id
 * @property string $email
 * @property string $phone
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $link
 * @property array $properties
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserOAuth2 extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'user_oauth2';
    /**
     * @inheritDoc
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    /**
     * @inheritDoc
     */
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('user_oauth2_attach.user_model.class'),
            'user_id',
            'id'
        );
    }

    /**
     * @param Model $user
     * @param string $service
     * @param string $externalId
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $gender
     * @param string|null $link
     * @param array|null $properties
     * @return self
     */
    public static function register(
        Model $user,
        string $service,
        string $externalId,
        ?string $email,
        ?string $phone,
        ?string $firstName,
        ?string $lastName,
        ?string $gender,
        ?string $link,
        ?array $properties
    ): self {
        $model = new self();
        $model->service = $service;
        $model->external_id = $externalId;
        $model->email = $email;
        $model->phone = $phone;
        $model->first_name = $firstName;
        $model->last_name = $lastName;
        $model->gender = $gender;
        $model->link = $link;
        $model->properties = $properties;
        $model->user()->associate($user);

        return $model;
    }
}
