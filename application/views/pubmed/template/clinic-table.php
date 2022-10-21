<style>
    .table-list{
        font-size: 12px;
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed
    }

    .table-list table, 
    .table-list tr, 
    .table-list td, 
    .table-list th{
        border: 1px solid #DCDCDC;
        word-break: break-all;
        word-wrap: break-word;
    }

    .table-list td{
        padding: 0px 10px;
        vertical-align: top;
        padding-top: 10px;
    }

    .data-row{
        border-bottom: 1px solid #DCDCDC;
        padding-bottom: 5px;
        margin-bottom: 5px;
    }

    .data-row:last-of-type{
        border-bottom: 0px;
    }

    thead{
        background: #DFECDF;
    }

    ul{
        padding-left: 10px;
    }
</style>
<h4 style="text-align: center; margin-bottom: 10px;"><?php echo $title;?></h4>
<table class="table-list">
    <thead>
        <tr>
            <th style="width: 3%"></th>
            <th style="width: 5%">NCT Number</th>
            <th style="width: 7%">Title</th>
            <th style="width: 7%">Authors</th>
            <th style="width: 7%">Description</th>
            <th style="width: 7%">Identifier</th>            
            <th style="width: 7%">Dates</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        $clinic_index = 1;
        foreach($clinics as $clinic){
            ?>
            <tr>
                <td><?php echo $clinic_index?></td>
                <td><?php echo $clinic['guid']?></td>
                <td>
                    <div class="data-row">
                        <a href="<?php echo $clinic['link']?>"><?php echo $clinic['title']?></a>
                    </div>
                </td>
                <td>                   
                    <div class="data-row">
                        <?php echo $clinic['creator']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        <?php echo $clinic['description']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        <?php echo $clinic['identifier']?>
                    </div>
                </td>
                <td>
                    <div class="data-row">
                        <?php echo $clinic['pubdate']?>
                    </div>
                </td>
                
                <!-- <td>
                </td> -->
            </tr>
            <?php
            $clinic_index++;
        }
        ?>
    </tbody>
</table>