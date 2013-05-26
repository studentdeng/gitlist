<?php

class CSV {

    public $delimiter = ',';
    public $enclosure = '"';
    
    public function write($filename, $head, $dataList)
    {
        //header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.csv');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        
        $fp = fopen('php://output', 'a');
         
        // 将数据通过fputcsv写到文件句柄
        
        fputcsv($fp, $head, ";");
         
        // 计数器
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        
        foreach ($dataList as $k => $item) {
            $cnt ++;
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }

            foreach ($item as $i => $v) {
                //$result[$i] = $v;
                $result[$i] = iconv('utf-8', 'gbk', $v);
            }
            
            fputcsv($fp, $result, ';');
        }
    }

    /* parse()
     * 
     * Parses the CSV and returns it as an array
     * 
     * @param $file : string : required
     * @param $delimiter : string : optional
     * @param $enclosure : string : optional
    */

    public function parse($file,$delimiter = null, $enclosure = null) {
        
        // if file doesn't exist, fail
        if(!is_file($file)) {
            die("CSV Parse Failed: Passed $file which does not exist or is not readable by the server");
        }
        
        // instantiate temp object
        $CSV = new CSV();

        // if delimiter override, set it
        if($delimiter) {
            $CSV->delimiter = $delimiter;       
        }

        // if enclosure override, set it
        if($enclosure) {
            $CSV->$enclosure = $enclosure;      
        }

        // Create Return Array
        $csv = array();

        // Line Lenght Info
        $csv['line_count'] = 0;
        $csv["line_length"] = trim(`awk '{ if ( length > x ) { x = length } }END{ print x }' '$file'`);

        // Headers & Rows
        $csv['headers'] = array();
        $csv['rows'] = array();
        
        // Open the file
        $file_hook = fopen($file, "r");

        // if the file was opened
        if($file_hook){

            // get the first line 
            while (($buffer = fgets($file_hook)) !== false) {
                // get the headers if they're not set
                if(!$csv['headers']) {
                    
                    $value = iconv('gbk', 'utf-8', trim($buffer));
                    $csv['headers'] = explode($CSV->delimiter,$value);
                } else {
                    break;
                }

            } 

            // set the row number
            $row_number = 0;

            // while loop on the file
            while (!feof($file_hook)) {

                // add line data to row in return array
                $row_number++;
                $csv['line_count']++;
                $csv['rows'][$row_number] = fgetcsv($file_hook, $line_length,$CSV->delimiter,$CSV->enclosure);

                // loop through and map field to header
                foreach($csv['rows'][$row_number] as $id => $value) {
                    
                    $value = iconv('gbk', 'utf-8', $value);

                    // set header / cell association 
                    $csv['rows'][$row_number][$csv['headers'][$id]] = $value;
                    unset($csv['rows'][$row_number][$id]);

                } // end loop

            } // end while loop on file


        } //end if file opened

        // close file
        fclose($file);

        return $csv;

    } // end parse()

} // end CSV