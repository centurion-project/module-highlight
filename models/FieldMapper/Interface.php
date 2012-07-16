<?php

interface Highlight_Model_FieldMapper_Interface
{
    /**
     * takes a row for an arbitrary content type and returns an array of fields to be used
     * to display the row as a Highlight
     * @param $row the row to map
     * @return array
     * The array returned must follow this structure
     * array(
     *     'title'          => (string)
     *     'link'           => (string) a relative or absolute url to the content
     *     'description'    => (string)
     *     'cover'          => (Media_Model_DbTable_Row_File) a centurion image object.
     * )
     */
    public function map(Centurion_Db_Table_Row_Abstract $row);

    /**
     * takes a rowset and runs every entry into the map method.
     * @param SeekableIterator $rowset the rowset to map
     * @return array
     */
    public function mapRowSet($rowset);
}
