<?php
/**
 * DokuWiki Plugin since (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  jno <jno@pisem.net>
 *
 * Syntax:     <since>ISO-date</since>
 *
 * Renders as:
 *   <span class='since'> ... </span>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_since_since extends DokuWiki_Syntax_Plugin {
    public function getType() {
        // return 'FIXME: container|baseonly|formatting|substition|protected|disabled|paragraphs';
        return 'protected';
    }

    public function getPType() {
        // return 'FIXME: normal|block|stack';
        return 'block';
    }

    public function getSort() {
        return 195;
    }


    public function connectTo($mode) {
      $this->Lexer->addEntryPattern('<since(?=[^\r\n]*?>.*?</since>)',$mode,'plugin_since_since');
    }

    public function postConnect() {
      $this->Lexer->addExitPattern('</since>', 'plugin_since_since');
    }

    public function handle($match, $state, $pos, &$handler){
        switch ($state) {
          case DOKU_LEXER_ENTER:
            $this->syntax = substr($match, 1);
            return false;

          case DOKU_LEXER_UNMATCHED:
             // will include everything from <since ... to ... </since >
             // e.g. ... [attr] > [content]
             list($attr, $content) = preg_split('/>/u',$match,2);
         $content = explode(',',$content);
         // $attr reserved for future use

             return array($this->syntax, trim($attr), $content);
        }
        return false;
    }

    public function render($mode, &$renderer, $data) {
      if($mode != 'xhtml') return false;
      $now = new DateTime();
      // if (count($data) == 1) {
        list($syntax, $attr, $content) = $data;
        if ($syntax != 'since') return false;
    $SinceDate = '';
    foreach($content as $date) {
      $d = date_create( trim($date) );
      if( $d === FALSE ) {
        $SinceDate .= "&lt;bad date '$date'&gt;";
        break;
      }
      $interval = $d->diff($now);
      $s = $interval->format('%R');
      $s1 = ($s == '-') ? 'in&nbsp;' : '';
      $s2 = ($s == '-') ? '' : '&nbsp;ago';
      $SinceDate .= trim($date).'&nbsp;'.'<span class="since">'
              .$s1.$interval->format('%yy %mm %dd').$s2
              .'</span>';
    }
    $renderer->doc .= $SinceDate;
        return true;
      // }
      //return false;
    }
}

// vim:ts=4:sw=4:et:
