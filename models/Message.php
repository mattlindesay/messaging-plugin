<?php

namespace mattlindesay\messaging\models;

use Model;

/**
 * Model.
 */
class Message extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    public $fillable = ['user_id','sender_id','recipient_id','folder_id','subject','body','uuid'];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    //public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mattlindesay_messaging_messages';

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'sender' => ['RainLab\User\Models\User', 'table' => 'users'],
        'recipient' => ['RainLab\User\Models\User', 'table' => 'users'],
    ];

    /**
     * The attributes on which the post list can be ordered
     * @var array
     */
    public static $allowedSortingOptions = array(
        'subject asc' => 'Subject (ascending)',
        'subject desc' => 'Subject (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        // 'published_at asc' => 'Published (ascending)',
        // 'published_at desc' => 'Published (descending)',
        // 'random' => 'Random'
    );

    /**
     * Lists messages for the front end.
     *
     * @param array $options Display options
     *
     * @return self
     */
    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page' => 1,
            'perPage' => 30,
            'sort' => 'created_at',
            // 'categories' => null,
            // 'category'   => null,
            'search' => '',
            'folder_id' => 0,
            // 'published'  => true
        ], $options));

        //echo '<pre>';print_r($options);exit;

        $searchableFields = ['subject', 'body', 'folder_id'];

        // if ($published) {
        //     $query->isPublished();
        // }

        /*
         * Sorting
         */
        if (!is_array($sort)) {
            $sort = [$sort];
        }

        foreach ($sort as $_sort) {
            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);
                if (count($parts) < 2) {
                    array_push($parts, 'desc');
                }
                list($sortField, $sortDirection) = $parts;
                if ($sortField == 'random') {
                    $sortField = DB::raw('RAND()');
                }
                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        /*
         * Limit results to the folder in question
         */
        //if ($options['folder_id']) {
            $query->where('user_id', $options['user_id']);
            $query->where('folder_id', $options['folder_id']);
        //}
        

        /*
         * Categories
         */
        // if ($categories !== null) {
        //     if (!is_array($categories)) {
        //         $categories = [$categories];
        //     }
        //     $query->whereHas('categories', function ($q) use ($categories) {
        //         $q->whereIn('id', $categories);
        //     });
        // }

        /*
         * Category, including children
         */
        // if ($category !== null) {
        //     $category = Category::find($category);

        //     $categories = $category->getAllChildrenAndSelf()->lists('id');
        //     $query->whereHas('categories', function ($q) use ($categories) {
        //         $q->whereIn('id', $categories);
        //     });
        // }

        return $query->paginate($perPage, $page);
    }
}
