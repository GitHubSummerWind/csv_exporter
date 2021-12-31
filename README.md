# CsvExporter
laravel-admin csv export extends

# 安装 
```
composer require summer-wind/csv-exporter
```

# 创建导出类
```
<?php


namespace App\Admin\Extensions\Exports;

use Illuminate\Database\Eloquent\Model;
use SummerWind\CsvExporter\AdminCsvExporter;

class UserExport extends AdminCsvExporter
{

    /**
     * 导出文件名
     * @var string 
     */
    public $fileName = 'test.csv';

    /**
     * 导出标题
     * @var string[] 
     */
    public $headings = [
        'ID','用户名','创建时间','用户角色'
    ];

    /**
     * 导出查询字段 默认 *
     * @var string[] 
     */
    public $columns = [
        'id', 'username', 'created_at', 'role_id'
    ];

    /**
     * 导出设置映射
     * @param Model $model
     * @return array
     */
    public function map(Model $model)
    {
        return [
            $model->id,
            $model->username,
            $model->created_at,
            data_get($model,'role.name','')
        ];
    }
}
```