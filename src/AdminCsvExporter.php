<?php
namespace Zjh\Exporter;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Illuminate\Database\Eloquent\Model;

class AdminCsvExporter extends AbstractExporter {

    public function __construct(Grid $grid = null)
    {
        parent::__construct($grid);
    }

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var array
     */
    protected $headings = [];

    /**
     * @var array
     */
    protected $columns = ['*'];

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function query()
    {
        if (!empty($this->columns)) {
            return $this->getQuery()->select($this->columns);
        }

        return $this->getQuery();
    }

    public function export()
    {
        $this->csv($this->fileName,$this->headings,$this->query());
    }

    public function map(Model $model){
        return [];
    }

    /**
     * 导出csv
     *
     * @param String $sFileName
     * @param array $aTitle
     * @param $oQuery
     * @author zjh
     */
    protected function csv($sFileName,Array $aTitle, $oQuery)
    {
        // 设置过期时间
        set_time_limit(0);
        //处理需要导出的数据
        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'. $sFileName .'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        fwrite($fp, "\xEF\xBB\xBF"); // 写入bom 头 可识别 utf8
        fputcsv($fp, $aTitle);//将数据格式化为CSV格式并写入到output流中
        $accessNum = $oQuery->count();//从数据库获取总量，假设是一百万

        $perSize = 10000;//每次查询的条数
        $pages   = ceil($accessNum / $perSize);

        // 导出全部 和 选择行
        for($i = 1; $i <= $pages; $i++) {
            $oCollection = $oQuery->get();
            foreach($oCollection as $obj) {
                $rowData = $this->map($obj); //返回 array
                fputcsv($fp, $rowData);
            }
            unset($oCollection);//释放变量的内存
            //刷新输出缓冲到浏览器
            if (ob_get_level() > 0) {
                ob_flush();
            }
        }
        fclose($fp);
        exit();
    }

}
