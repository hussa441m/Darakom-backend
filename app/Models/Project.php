<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rating;



class Project extends Model
{ 


    protected $fillable = ['start_date' , 'end_date' , 'duration' , 'area' , 'location_details' , 'description' , 'building_no'  ,'budget' ,   'status' , 'rate' , 'comment' ,'project_type_id', 'customer_id' , 'performed_by','province_id'];
    
    function province(){
        return $this->belongsTo(province::class);
    }
    function projectType(){
        return $this->belongsTo(ProjectType::class);
    }
    
    function offers(){
        return $this->hasMany(Offer::class);
    }
    function customer(){
        return $this->belongsTo(User::class,'customer_id');
    }
    function client(){
        return $this->belongsTo(Profile::class,'performed_by');
    }
    
    function documents(){
        return $this->morphMany(Document::class , 'documentable');
    }

    function steps(){
        return $this->hasMany(Step::class);
    }
/**
 * جميع التقييمات الخاصة بالمشروع
 */
    public function ratings() {
        return $this->hasMany(Rating::class);
    }

}
