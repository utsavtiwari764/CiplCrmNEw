<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Trebol\Entrust\Traits\EntrustUserTrait;
use Laravel\Passport\HasApiTokens;
class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait,HasFactory,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'image',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends =[
        'profile_image_url', 'mobile_with_code', 'formatted_mobile'
    ];

    public function getProfileImageUrlAttribute(){
        if(is_null($this->image)){
            return asset('avatar.png');
        }
        return asset_url_local_s3('profile/'.$this->image);
    }
     
    
    public function role() {
        return $this->hasOne(RoleUser::class, 'user_id');
    }

    public function todoItems()
    {
        return $this->hasMany(TodoItem::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(InterviewSchedule::class, InterviewScheduleEmployee::class);
    }

    public static function allAdmins($exceptId = NULL)
    {
        $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.calling_code', 'users.mobile', 'users.mobile_verified', 'users.created_at')
            ->where('roles.id', 1);

        if(!is_null($exceptId)){
            $users->where('users.id', '<>', $exceptId);
        }

        return $users->get();
    }

    public function getMobileWithCodeAttribute()
    {
        return substr($this->calling_code, 1).$this->mobile;
    }

    public function getFormattedMobileAttribute()
    {
        if (!$this->calling_code) {
            return $this->mobile;
        }
        return $this->calling_code.'-'.$this->mobile;
    }

    public function routeNotificationForNexmo($notification)
    {
        return $this->mobile_with_code;
    }
}
