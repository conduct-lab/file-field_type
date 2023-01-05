<?php namespace Anomaly\FileFieldType\Table;

use Anomaly\FilesModule\File\FileModel;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ValueTableBuilder
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class ValueTableBuilder extends TableBuilder
{

    /**
     * The uploaded IDs.
     *
     * @var array
     */
    protected $uploaded = [];

    /**
     * The table model.
     *
     * @var string
     */
    protected $model = FileModel::class;

    /**
     * The table columns.
     *
     * @var array
     */
    protected $columns = [
        'entry.preview' => [
            'heading' => 'anomaly.module.files::field.preview.name',
        ],
        'name'          => [
            'wrapper'     => '
                    <strong>{value.file}</strong>
                    <br>
                    <small class="text-muted">{value.disk}://{value.folder}/{value.file}</small>
                    <br>
                    <span><span class="tag {value.size_tag_type} tag-sm">{value.readable_file_size}</span> <span class="tag tag-default tag-sm text-gray-dark">{value.mime_type}</span> {value.size} {value.keywords}</span>',
            'value'       => [
                'file'     => 'entry.name',
                'mime_type'     => 'entry.mime_type',
                'folder'   => 'entry.folder.slug',
                'keywords' => 'entry.keywords.labels|join',
                'disk'     => 'entry.folder.disk.slug',
                'size'     => 'entry.size_label',
                'size_tag_type' => 'entry.size.value < 150000 ? "tag-info" : entry.size.value < 350000 ? "tag-warning" : "tag-danger"',
                'readable_file_size' => 'entry.readable_size',
            ],
        ],
        'folder'          => [
            'wrapper'     => '<small class="text-muted">Folder:</small><br>{value.folder}<br><small class="text-muted">&nbsp;</small>',
            'value'       => [
                'folder' => 'entry.folder.name'
            ],
        ],
        'updated_at'          => [
            'wrapper'     => '<small class="text-muted">Updated:</small><br>{value.updated_at_human}
                              <br>
                              <small class="text-muted">{value.updated_at}</small>',
            'value'       => [
                'updated_at' => 'entry.updated_at.format("M jS, Y, H:i")',
                'updated_at_human' => 'entry.updated_at.diffForHumans()'
            ],
        ],
    ];

    /**
     * The table buttons.
     *
     * @var array
     */
    protected $buttons = [
        'edit'   => [
            'target'     => '_blank',
            'href'       => 'admin/files/edit/{entry.id}',
            'permission' => 'anomaly.module.files::files.write',
        ],
        'view'   => [
            'target' => '_blank',
            'href'   => 'admin/files/view/{entry.id}',
        ],
        'remove' => [
            'data-dismiss' => 'file',
        ],
    ];

    /**
     * The table options.
     *
     * @var array
     */
    protected $options = [
        'limit'              => 1,
        'show_headers'       => false,
        'sortable_headers'   => false,
        'table_view'         => 'anomaly.field_type.file::table',
        'no_results_message' => 'anomaly.field_type.file::message.no_file_selected',
    ];

    /**
     * The table assets.
     *
     * @var array
     */
    protected $assets = [
        'styles.css' => [
            'anomaly.field_type.file::less/input.less',
        ],
    ];

    /**
     * Fired just before querying
     * for table entries.
     *
     * @param Builder $query
     */
    public function onQuerying(Builder $query)
    {
        $uploaded = $this->getUploaded();

        $query->whereIn('id', $uploaded ?: [0]);

        $query->orderBy('updated_at', 'ASC');
        $query->orderBy('created_at', 'ASC');
    }

    /**
     * Get uploaded IDs.
     *
     * @return array
     */
    public function getUploaded()
    {
        return $this->uploaded;
    }

    /**
     * Set the uploaded IDs.
     *
     * @param  array $uploaded
     * @return $this
     */
    public function setUploaded(array $uploaded)
    {
        $this->uploaded = $uploaded;

        return $this;
    }
}
