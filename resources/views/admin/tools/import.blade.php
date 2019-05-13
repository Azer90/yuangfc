<div class="btn-group" style="margin-right: 10px">
    <a class="btn btn-sm btn-twitter"  title="导入">
        <i class="fa fa-upload"></i>
        <input class="import" style="position: absolute;right: 0;top: 0;font-size: 17px;width: 60px;opacity:0;filter: alpha(opacity=0);" accept="application/vnd.sealed-xls" type="file" name="fileUpload"/>
        <span class="hidden-xs">导入</span>
    </a>
    <button type="button" class="btn btn-sm btn-twitter dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li><a href="{{$url}}" target="_blank">下载模板</a></li>
    </ul>
</div>
<style>
    .speed{
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1050;
        display: none;
        overflow: hidden;
        -webkit-overflow-scrolling: touch;
        outline: 0;
    }
    .state{
        color: red;
    }
</style>
<div class="speed" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">导入数据</h4>
            </div>
            <div class="modal-body">
                <p><span class="file_name"></span><span class="state" style="margin-left: 50px">正在导入...</span></p>
                <p><span style="margin-right: 20px">成功导入：<span class="success_count">0</span>条</span><span>导入失败：<span class="error_count">0</span>条</span></p>
                <p class="error_table"></p>
            </div>
        </div>
    </div>
</div>