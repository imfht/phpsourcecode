/*
pyte.charsets
~~~~~~~~~~~~~~

This module defines ``G0`` and ``G1`` charset mappings the same way
they are defined for linux terminal, see
``linux/drivers/tty/consolemap.c`` @ http://git.kernel.org

.. note:: ``VT100_MAP`` and ``IBMPC_MAP`` were taken unchanged
          from linux kernel source and therefore are licensed
          under **GPL**.

:copyright: (c) 2011 by Selectel, see AUTHORS for more details.
:license: LGPL, see LICENSE for more details.
*/

// Latin1
var LAT1_MAP = [];

// VT100 graphic character set
var VT100_MAP = [];

// IBM Codepage 437
var IBMPC_MAP = [];

// VAX42 character set
var VAX42_MAP = [];

var MAPS = {
    "B": LAT1_MAP,
    "0": VT100_MAP,
    "U": IBMPC_MAP,
    "V": VAX42_MAP
};

(function() {

    var i;
    for (i = 0; i < 256; i++) LAT1_MAP.push(String.fromCharCode(i));

    var vt100map = [
        0x0000, 0x0001, 0x0002, 0x0003, 0x0004, 0x0005, 0x0006, 0x0007,
        0x0008, 0x0009, 0x000a, 0x000b, 0x000c, 0x000d, 0x000e, 0x000f,
        0x0010, 0x0011, 0x0012, 0x0013, 0x0014, 0x0015, 0x0016, 0x0017,
        0x0018, 0x0019, 0x001a, 0x001b, 0x001c, 0x001d, 0x001e, 0x001f,
        0x0020, 0x0021, 0x0022, 0x0023, 0x0024, 0x0025, 0x0026, 0x0027,
        0x0028, 0x0029, 0x002a, 0x2192, 0x2190, 0x2191, 0x2193, 0x002f,
        0x2588, 0x0031, 0x0032, 0x0033, 0x0034, 0x0035, 0x0036, 0x0037,
        0x0038, 0x0039, 0x003a, 0x003b, 0x003c, 0x003d, 0x003e, 0x003f,
        0x0040, 0x0041, 0x0042, 0x0043, 0x0044, 0x0045, 0x0046, 0x0047,
        0x0048, 0x0049, 0x004a, 0x004b, 0x004c, 0x004d, 0x004e, 0x004f,
        0x0050, 0x0051, 0x0052, 0x0053, 0x0054, 0x0055, 0x0056, 0x0057,
        0x0058, 0x0059, 0x005a, 0x005b, 0x005c, 0x005d, 0x005e, 0x00a0,
        0x25c6, 0x2592, 0x2409, 0x240c, 0x240d, 0x240a, 0x00b0, 0x00b1,
        0x2591, 0x240b, 0x2518, 0x2510, 0x250c, 0x2514, 0x253c, 0x23ba,
        0x23bb, 0x2500, 0x23bc, 0x23bd, 0x251c, 0x2524, 0x2534, 0x252c,
        0x2502, 0x2264, 0x2265, 0x03c0, 0x2260, 0x00a3, 0x00b7, 0x007f,
        0x0080, 0x0081, 0x0082, 0x0083, 0x0084, 0x0085, 0x0086, 0x0087,
        0x0088, 0x0089, 0x008a, 0x008b, 0x008c, 0x008d, 0x008e, 0x008f,
        0x0090, 0x0091, 0x0092, 0x0093, 0x0094, 0x0095, 0x0096, 0x0097,
        0x0098, 0x0099, 0x009a, 0x009b, 0x009c, 0x009d, 0x009e, 0x009f,
        0x00a0, 0x00a1, 0x00a2, 0x00a3, 0x00a4, 0x00a5, 0x00a6, 0x00a7,
        0x00a8, 0x00a9, 0x00aa, 0x00ab, 0x00ac, 0x00ad, 0x00ae, 0x00af,
        0x00b0, 0x00b1, 0x00b2, 0x00b3, 0x00b4, 0x00b5, 0x00b6, 0x00b7,
        0x00b8, 0x00b9, 0x00ba, 0x00bb, 0x00bc, 0x00bd, 0x00be, 0x00bf,
        0x00c0, 0x00c1, 0x00c2, 0x00c3, 0x00c4, 0x00c5, 0x00c6, 0x00c7,
        0x00c8, 0x00c9, 0x00ca, 0x00cb, 0x00cc, 0x00cd, 0x00ce, 0x00cf,
        0x00d0, 0x00d1, 0x00d2, 0x00d3, 0x00d4, 0x00d5, 0x00d6, 0x00d7,
        0x00d8, 0x00d9, 0x00da, 0x00db, 0x00dc, 0x00dd, 0x00de, 0x00df,
        0x00e0, 0x00e1, 0x00e2, 0x00e3, 0x00e4, 0x00e5, 0x00e6, 0x00e7,
        0x00e8, 0x00e9, 0x00ea, 0x00eb, 0x00ec, 0x00ed, 0x00ee, 0x00ef,
        0x00f0, 0x00f1, 0x00f2, 0x00f3, 0x00f4, 0x00f5, 0x00f6, 0x00f7,
        0x00f8, 0x00f9, 0x00fa, 0x00fb, 0x00fc, 0x00fd, 0x00fe, 0x00ff
    ];

    for (i = 0; i < vt100map.length; i++) VT100_MAP.push(String.fromCharCode(vt100map[i]));

    var ibmpcmap = [
        0x0000, 0x263a, 0x263b, 0x2665, 0x2666, 0x2663, 0x2660, 0x2022,
        0x25d8, 0x25cb, 0x25d9, 0x2642, 0x2640, 0x266a, 0x266b, 0x263c,
        0x25b6, 0x25c0, 0x2195, 0x203c, 0x00b6, 0x00a7, 0x25ac, 0x21a8,
        0x2191, 0x2193, 0x2192, 0x2190, 0x221f, 0x2194, 0x25b2, 0x25bc,
        0x0020, 0x0021, 0x0022, 0x0023, 0x0024, 0x0025, 0x0026, 0x0027,
        0x0028, 0x0029, 0x002a, 0x002b, 0x002c, 0x002d, 0x002e, 0x002f,
        0x0030, 0x0031, 0x0032, 0x0033, 0x0034, 0x0035, 0x0036, 0x0037,
        0x0038, 0x0039, 0x003a, 0x003b, 0x003c, 0x003d, 0x003e, 0x003f,
        0x0040, 0x0041, 0x0042, 0x0043, 0x0044, 0x0045, 0x0046, 0x0047,
        0x0048, 0x0049, 0x004a, 0x004b, 0x004c, 0x004d, 0x004e, 0x004f,
        0x0050, 0x0051, 0x0052, 0x0053, 0x0054, 0x0055, 0x0056, 0x0057,
        0x0058, 0x0059, 0x005a, 0x005b, 0x005c, 0x005d, 0x005e, 0x005f,
        0x0060, 0x0061, 0x0062, 0x0063, 0x0064, 0x0065, 0x0066, 0x0067,
        0x0068, 0x0069, 0x006a, 0x006b, 0x006c, 0x006d, 0x006e, 0x006f,
        0x0070, 0x0071, 0x0072, 0x0073, 0x0074, 0x0075, 0x0076, 0x0077,
        0x0078, 0x0079, 0x007a, 0x007b, 0x007c, 0x007d, 0x007e, 0x2302,
        0x00c7, 0x00fc, 0x00e9, 0x00e2, 0x00e4, 0x00e0, 0x00e5, 0x00e7,
        0x00ea, 0x00eb, 0x00e8, 0x00ef, 0x00ee, 0x00ec, 0x00c4, 0x00c5,
        0x00c9, 0x00e6, 0x00c6, 0x00f4, 0x00f6, 0x00f2, 0x00fb, 0x00f9,
        0x00ff, 0x00d6, 0x00dc, 0x00a2, 0x00a3, 0x00a5, 0x20a7, 0x0192,
        0x00e1, 0x00ed, 0x00f3, 0x00fa, 0x00f1, 0x00d1, 0x00aa, 0x00ba,
        0x00bf, 0x2310, 0x00ac, 0x00bd, 0x00bc, 0x00a1, 0x00ab, 0x00bb,
        0x2591, 0x2592, 0x2593, 0x2502, 0x2524, 0x2561, 0x2562, 0x2556,
        0x2555, 0x2563, 0x2551, 0x2557, 0x255d, 0x255c, 0x255b, 0x2510,
        0x2514, 0x2534, 0x252c, 0x251c, 0x2500, 0x253c, 0x255e, 0x255f,
        0x255a, 0x2554, 0x2569, 0x2566, 0x2560, 0x2550, 0x256c, 0x2567,
        0x2568, 0x2564, 0x2565, 0x2559, 0x2558, 0x2552, 0x2553, 0x256b,
        0x256a, 0x2518, 0x250c, 0x2588, 0x2584, 0x258c, 0x2590, 0x2580,
        0x03b1, 0x00df, 0x0393, 0x03c0, 0x03a3, 0x03c3, 0x00b5, 0x03c4,
        0x03a6, 0x0398, 0x03a9, 0x03b4, 0x221e, 0x03c6, 0x03b5, 0x2229,
        0x2261, 0x00b1, 0x2265, 0x2264, 0x2320, 0x2321, 0x00f7, 0x2248,
        0x00b0, 0x2219, 0x00b7, 0x221a, 0x207f, 0x00b2, 0x25a0, 0x00a0
    ];

    for (i = 0; i < ibmpcmap.length; i++) IBMPC_MAP.push(String.fromCharCode(ibmpcmap[i]));

    vax42map = [
        0x0000, 0x263a, 0x263b, 0x2665, 0x2666, 0x2663, 0x2660, 0x2022,
        0x25d8, 0x25cb, 0x25d9, 0x2642, 0x2640, 0x266a, 0x266b, 0x263c,
        0x25b6, 0x25c0, 0x2195, 0x203c, 0x00b6, 0x00a7, 0x25ac, 0x21a8,
        0x2191, 0x2193, 0x2192, 0x2190, 0x221f, 0x2194, 0x25b2, 0x25bc,
        0x0020, 0x043b, 0x0022, 0x0023, 0x0024, 0x0025, 0x0026, 0x0027,
        0x0028, 0x0029, 0x002a, 0x002b, 0x002c, 0x002d, 0x002e, 0x002f,
        0x0030, 0x0031, 0x0032, 0x0033, 0x0034, 0x0035, 0x0036, 0x0037,
        0x0038, 0x0039, 0x003a, 0x003b, 0x003c, 0x003d, 0x003e, 0x0435,
        0x0040, 0x0041, 0x0042, 0x0043, 0x0044, 0x0045, 0x0046, 0x0047,
        0x0048, 0x0049, 0x004a, 0x004b, 0x004c, 0x004d, 0x004e, 0x004f,
        0x0050, 0x0051, 0x0052, 0x0053, 0x0054, 0x0055, 0x0056, 0x0057,
        0x0058, 0x0059, 0x005a, 0x005b, 0x005c, 0x005d, 0x005e, 0x005f,
        0x0060, 0x0441, 0x0062, 0x0063, 0x0064, 0x0065, 0x0066, 0x0067,
        0x0435, 0x0069, 0x006a, 0x006b, 0x006c, 0x006d, 0x006e, 0x043a,
        0x0070, 0x0071, 0x0442, 0x0073, 0x043b, 0x0435, 0x0076, 0x0077,
        0x0078, 0x0079, 0x007a, 0x007b, 0x007c, 0x007d, 0x007e, 0x2302,
        0x00c7, 0x00fc, 0x00e9, 0x00e2, 0x00e4, 0x00e0, 0x00e5, 0x00e7,
        0x00ea, 0x00eb, 0x00e8, 0x00ef, 0x00ee, 0x00ec, 0x00c4, 0x00c5,
        0x00c9, 0x00e6, 0x00c6, 0x00f4, 0x00f6, 0x00f2, 0x00fb, 0x00f9,
        0x00ff, 0x00d6, 0x00dc, 0x00a2, 0x00a3, 0x00a5, 0x20a7, 0x0192,
        0x00e1, 0x00ed, 0x00f3, 0x00fa, 0x00f1, 0x00d1, 0x00aa, 0x00ba,
        0x00bf, 0x2310, 0x00ac, 0x00bd, 0x00bc, 0x00a1, 0x00ab, 0x00bb,
        0x2591, 0x2592, 0x2593, 0x2502, 0x2524, 0x2561, 0x2562, 0x2556,
        0x2555, 0x2563, 0x2551, 0x2557, 0x255d, 0x255c, 0x255b, 0x2510,
        0x2514, 0x2534, 0x252c, 0x251c, 0x2500, 0x253c, 0x255e, 0x255f,
        0x255a, 0x2554, 0x2569, 0x2566, 0x2560, 0x2550, 0x256c, 0x2567,
        0x2568, 0x2564, 0x2565, 0x2559, 0x2558, 0x2552, 0x2553, 0x256b,
        0x256a, 0x2518, 0x250c, 0x2588, 0x2584, 0x258c, 0x2590, 0x2580,
        0x03b1, 0x00df, 0x0393, 0x03c0, 0x03a3, 0x03c3, 0x00b5, 0x03c4,
        0x03a6, 0x0398, 0x03a9, 0x03b4, 0x221e, 0x03c6, 0x03b5, 0x2229,
        0x2261, 0x00b1, 0x2265, 0x2264, 0x2320, 0x2321, 0x00f7, 0x2248,
        0x00b0, 0x2219, 0x00b7, 0x221a, 0x207f, 0x00b2, 0x25a0, 0x00a0
    ];

    for (i = 0; i < vax42map.length; i++) VAX42_MAP.push(String.fromCharCode(vax42map[i]));


})();//: *Space*: Not suprisingly -- ``" "``.
var SP = " ";

//: *Null*: Does nothing.
var NUL = "\u0000";

//: *Bell*: Beeps.
var BEL = "\u0007";

//: *Backspace*: Backspace one column, but not past the begining of the
//: line.
var BS = "\u0008";

//: *Horizontal tab*: Move cursor to the next tab stop, or to the end
//: of the line if there is no earlier tab stop.
var HT = "\u0009";

//: *Linefeed*: Give a line feed, and, if :data:`pyte.modes.LNM` (new
//: line mode) is set also a carriage return.
var LF = "\n";
//: *Vertical tab*: Same as :data:`LF`.
var VT = "\u000b";
//: *Form feed*: Same as :data:`LF`.
var FF = "\u000c";

//: *Carriage return*: Move cursor to left margin on current line.
var CR = "\r";

//: *Shift out*: Activate G1 character set.
var SO = "\u000e";

//: *Shift in*: Activate G0 character set.
var SI = "\u000f";

//: *Cancel*: Interrupt escape sequence. If received during an escape or
//: control sequence, cancels the sequence and displays substitution
//: character.
var CAN = "\u0018";
//: *Substitute*: Same as :data:`CAN`.
var SUB = "\u001a";

//: *Escape*: Starts an escape sequence.
var ESC = "\u001b";

//: *Delete*: Is ingored.
var DEL = "\u007f";

//: *Control sequence introducer*: An equavalent for ``ESC [``.
var CSI = "\u009b";/*
    pyte.escape
    ~~~~~~~~~~~

    This module defines bot CSI and non-CSI escape sequences, recognized
    by :class:`~pyte.streams.Stream` and subclasses.

    :copyright: (c) 2011 by Selectel, see AUTHORS for more details.
    :license: LGPL, see LICENSE for more details.
*/


//: *Reset*.
var RIS = "c";

//: *Index*: Move cursor down one line in same column. If the cursor is
//: at the bottom margin, the screen performs a scroll-up.
var IND = "D";

//: *Next line*: Same as :data:`pyte.control.LF`.
var NEL = "E";

//: Tabulation set: Set a horizontal tab stop at cursor position.
var HTS = "H";

//: *Reverse index*: Move cursor up one line in same column. If the
//: cursor is at the top margin, the screen performs a scroll-down.
var RI = "M";

//: Save cursor: Save cursor position, character attribute (graphic
//: rendition), character set, and origin mode selection (see
//: :data:`DECRC`).
var DECSC = "7";

//: *Restore cursor*: Restore previously saved cursor position, character
//: attribute (graphic rendition), character set, and origin mode
//: selection. If none were saved, move cursor to home position.
var DECRC = "8";


// "Sharp" escape sequences.
// -------------------------

//: *Alignment display*: Fill screen with uppercase E's for testing
//: screen focus and alignment.
var DECALN = "8";


// ECMA-48 CSI sequences.
// ---------------------

//: *Insert character*: Insert the indicated // of blank characters.
var ICH = "@";

//: *Cursor up*: Move cursor up the indicated // of lines in same column.
//: Cursor stops at top margin.
var CUU = "A";

//: *Cursor down*: Move cursor down the indicated // of lines in same
//: column. Cursor stops at bottom margin.
var CUD = "B";

//: *Cursor forward*: Move cursor right the indicated // of columns.
//: Cursor stops at right margin.
var CUF = "C";

//: *Cursor back*: Move cursor left the indicated // of columns. Cursor
//: stops at left margin.
var CUB = "D";

//: *Cursor next line*: Move cursor down the indicated // of lines to
//: column 1.
var CNL = "E";

//: *Cursor previous line*: Move cursor up the indicated // of lines to
//: column 1.
var CPL = "F";

//: *Cursor horizontal align*: Move cursor to the indicated column in
//: current line.
var CHA = "G";

//: *Cursor position*: Move cursor to the indicated line, column (origin
//: at ``1, 1``).
var CUP = "H";

//: *Erase data* (default: from cursor to end of line).
var ED = "J";

//: *Erase in line* (default: from cursor to end of line).
var EL = "K";

//: *Insert line*: Insert the indicated // of blank lines, starting from
//: the current line. Lines displayed below cursor move down. Lines moved
//: past the bottom margin are lost.
var IL = "L";

//: *Delete line*: Delete the indicated // of lines, starting from the
//: current line. As lines are deleted, lines displayed below cursor
//: move up. Lines added to bottom of screen have spaces with same
//: character attributes as last line move up.
var DL = "M";

//: *Delete character*: Delete the indicated // of characters on the
//: current line. When character is deleted, all characters to the right
//: of cursor move left.
var DCH = "P";

//: *Erase character*: Erase the indicated // of characters on the
//: current line.
var ECH = "X";

//: *Horizontal position relative*: Same as :data:`CUF`.
var HPR = "a";

//: *Vertical position adjust*: Move cursor to the indicated line,
//: current column.
var VPA = "d";

//: *Vertical position relative*: Same as :data:`CUD`.
var VPR = "e";

//: *Horizontal / Vertical position*: Same as :data:`CUP`.
var HVP = "f";

//: *Tabulation clear*: Clears a horizontal tab stop at cursor position.
var TBC = "g";

//: *Set mode*.
var SM = "h";

//: *Reset mode*.
var RM = "l";

//: *Select graphics rendition*: The terminal can display the following
//: character attributes that change the character display without
//: changing the character (see :mod:`pyte.graphics`).
var SGR = "m";

//: *Select top and bottom margins*: Selects margins, defining the
//: scrolling region; parameters are top and bottom line. If called
//: without any arguments, whole screen is used.
var DECSTBM = "r";

//: *Horizontal position adjust*: Same as :data:`CHA`.
var HPA = "'";
// -*- coding: utf-8 -*-
/*
    pyte.graphics
    ~~~~~~~~~~~~~

    This module defines graphic-related constants, mostly taken from
    :manpage:`console_codes(4)` and
    http://pueblo.sourceforge.net/doc/manual/ansi_color_codes.html.

    :copyright: (c) 2011 by Selectel, see AUTHORS for more details.
    :license: LGPL, see LICENSE for more details.
*/

//: A mapping of ANSI text style codes to style names, "+" means the:
//: attribute is set, "-" -- reset; example:
//:
//: >>> text[1]
//: '+bold'
//: >>> text[9]
//: '+strikethrough'
var TEXT = {
    1: "+bold" ,
    3: "+italics",
    4: "+underscore",
    7: "+reverse",
    9: "+strikethrough",
    22: "-bold",
    23: "-italics",
    24: "-underscore",
    27: "-reverse",
    29: "-strikethrough"
};


//: A mapping of ANSI foreground color codes to color names, example:
//:
//: >>> FG[30]
//: 'black'
//: >>> FG[38]
//: 'default'
var FG = {
    30: "black",
    31: "red",
    32: "green",
    33: "brown",
    34: "blue",
    35: "magenta",
    36: "cyan",
    37: "white",
    39: "default"  // white.
};

//: A mapping of ANSI background color codes to color names, example:
//:
//: >>> BG[40]
//: 'black'
//: >>> BG[48]
//: 'default'
var BG = {
    40: "black",
    41: "red",
    42: "green",
    43: "brown",
    44: "blue",
    45: "magenta",
    46: "cyan",
    47: "white",
    49: "default"  // black.
};

// Reverse mapping of all available attributes -- keep this private!
_SGR = {};
(function() {
    var k;
    for (k in BG)   _SGR[BG[k]] = k;
    for (k in FG)   _SGR[FG[k]] = k;
    for (k in TEXT) _SGR[TEXT[k]] = k;
})();// -*- coding: utf-8 -*-
/*
    pyte.modes
    ~~~~~~~~~~

    This module defines terminal mode switches, used by
    :class:`~pyte.screens.Screen`. There're two types of terminal modes:

    * `non-private` which should be set with ``ESC [ N h``, where ``N``
      is an integer, representing mode being set; and
    * `private` which should be set with ``ESC [ ? N h``.

    The latter are shifted 5 times to the right, to be easily
    distinguishable from the former ones; for example `Origin Mode`
    -- :data:`DECOM` is ``192`` not ``6``.

    >>> DECOM
    192

    :copyright: (c) 2011 by Selectel, see AUTHORS for more details.
    :license: LGPL, see LICENSE for more details.
*/

//: *Line Feed/New Line Mode*: When enabled, causes a received
//: :data:`~pyte.control.LF`, :data:`pyte.control.FF`, or
//: :data:`~pyte.control.VT` to move the cursor to the first column of
//: the next line.
var LNM = 20;

//: *Insert/Replace Mode*: When enabled, new display characters move
//: old display characters to the right. Characters moved past the
//: right margin are lost. Otherwise, new display characters replace
//: old display characters at the cursor position.
var IRM = 4;


// Private modes.
// ..............

//: *Text Cursor Enable Mode*: determines if the text cursor is
//: visible.
var DECTCEM = 25 << 5;

//: *Screen Mode*: toggles screen-wide reverse-video mode.
var DECSCNM = 5 << 5;

//: *Origin Mode*: allows cursor addressing relative to a user-defined
//: origin. This mode resets when the terminal is powered up or reset.
//: It does not affect the erase in display (ED) function.
var DECOM = 6 << 5;

//: *Auto Wrap Mode*: selects where received graphic characters appear
//: when the cursor is at the right margin.
var DECAWM = 7 << 5;

//: *Column Mode*: selects the number of columns per line (80 or 132)
//: on the screen.
var DECCOLM = 3 << 5;
/*
pyte.screens
~~~~~~~~~~~~

This module provides classes for terminal screens, currently
it contains three screens with different features:

* :class:`~pyte.screens.Screen` -- base screen implementation,
  which handles all the core escape sequences, recognized by
  :class:`~pyte.streams.Stream`.
* If you need a screen to keep track of the changed lines
  (which you probably do need) -- use
  :class:`~pyte.screens.DiffScreen`.
* If you also want a screen to collect history and allow
  pagination -- :class:`pyte.screen.HistoryScreen` is here
  for ya ;)

.. note:: It would be nice to split those features into mixin
          classes, rather than subclasses, but it's not obvious
          how to do -- feel free to submit a pull request.

:copyright: (c) 2011 Selectel, see AUTHORS for more details.
:license: LGPL, see LICENSE for more details.
*/

var namedlist = function(fields) { // something like namedtuple in python
    return function() {
        for(var i = 0; i < arguments.length; i++) {
            this[i] = arguments[i];
            this[fields[i]] = arguments[i];
        }
    };
};

function range() {
    var start, end, step;
    var array = [];

    switch (arguments.length) {
        case 0:
            throw new Error('range() expected at least 1 argument, got 0 - must be specified as [start,] stop[, step]');
            return array;
        case 1:
            start = 0;
            end = Math.floor(arguments[0]) - 1;
            step = 1;
            break;
        case 2:
        case 3:
        default:
            start = Math.floor(arguments[0]);
            end = Math.floor(arguments[1]) - 1;
            var s = arguments[2];
            if (typeof s === 'undefined') {
                s = 1;
            }
            step = Math.floor(s) || (function () {
                throw new Error('range() step argument must not be zero');
            })();
            break;
    }

    var i;
    if (step > 0) {
        for (i = start; i <= end; i += step) {
            array.push(i);
        }
    } else if (step < 0) {
        step = -step;
        if (start > end) {
            for (i = start; i > end + 1; i -= step) {
                array.push(i);
            }
        }
    }
    return array;
}

function repeat(num, obj) {
    var res = [];
    for (var i = 0; i < num; i++) res.push(obj);
    return res;
}

var Margins = namedlist(["top", "bottom"]);
var Savepoint = namedlist(["cursor","g0_charset","g1_charset","charset","origin","wrap"]);

var Char = function(data, attrs) {
    this.data = data;

    attrs = attrs || {};
    this.fg = attrs.fg || "default";
    this.bg = attrs.bg || "default";
    this.bold = attrs.bold || false;
    this.italics = attrs.italics || false;
    this.underscore = attrs.underscore || false;
    this.reverse = attrs.reverse || false;
    this.strikethrough = attrs.strikethrough || false;
};

var Cursor = function(x, y, attrs) {
    this.x = x;
    this.y = y;
    this.attrs = attrs || (new Char(" "));
    this.hidden = false;
};

var Screen = function() {
    var self = this;
    this.__init__.apply(this, arguments);
    /*
    // debug:
    for (var k in this) {
        if (typeof this[k] != 'function') continue;
        (function(k) {
            var orig = self[k];
            self[k] = function() {
                console.log(k, arguments);
                orig.apply(self, arguments);
            }
        })(''+k);

    }
    */
};

Screen.prototype = {
    default_char: new Char(" ", {fg: "default", "bg": "default"}),
    __init__: function(columns, lines) {
        this.savepoints = [];
        this.lines = lines;
        this.length = lines;
        this.columns = columns;
        this.history = [];
        this.reset();
    },
    __toString: function() {
        return "Screen("+this.columns+","+this.lines+")";
    },
    __before__: function(command) {

    },
    __after__: function(command) {

    },
    size: function() {
        return [this.lines, this.columns];
    },
    display: function() {
        /* Returns a :func:`list` of screen lines as unicode strings. */
        var lines = [];
        for (var i = 0; i < this.lines; i++) {
            var line = [];
            for (var j = 0; j < this.columns; j++) {
                line.push(this[i][j].data);
            }
            lines.push(line.join(""));
        }
        return lines;
    },
    reset: function() {
        var line;
        for (var i = 0; i < this.lines; i++) {
            line = [];
            for (var j = 0; j < this.columns; j++) line.push(this.default_char);
            this[i] = line;
        }
        this.mode = {};
        this.mode[DECAWM] = true;
        this.mode[DECTCEM] = true;
        this.mode[LNM] = true;

        this.margins = new Margins(0, this.lines - 1);

        this.charset = 0;
        this.g0_charset = IBMPC_MAP;
        this.g1_charset = VT100_MAP;

        var tabstops = range(8, this.columns, 8);
        this.tabstops = {};
        for (i = 0; i < tabstops.length; i++) this.tabstops[tabstops[i]] = true;

        this.cursor = new Cursor(0, 0);
        this.cursor_position();
    },
    resize: function(lines, columns) {
        lines = lines || this.lines;
        columns = columns || this.columns;

        var diff = this.lines - lines, i, y;

        if (diff < 0) {
            for (i = diff; i < 0; i++) {
                this.push(repeat(this.columns, this.default_char));
            }
        } else if (diff > 0) {
            for (i = 0; i < diff; i++) this.pop();
        }

        diff = this.columns - columns;

        if (diff < 0) {
            for (y = 0; y < lines; y++) {
                for (i = 0; i < -diff; i++) this[y].push(this.default_char);
            }
        } else if (diff > 0) {
            for (y = 0; y < lines; y++) {
                this[y].splice(0, diff);
            }
        }

        this.lines = lines;
        this.columns = columns;
        this.margins = new Margins(0, this.lines - 1);
        this.reset_mode(DECOM);
    },
    set_margins: function(top, bottom) {
        if (top === undefined || bottom === undefined) {
            return;
        }

        top = Math.max(0, Math.min(top - 1, this.lines - 1));
        bottom = Math.max(0, Math.min(bottom - 1, this.lines - 1));

        if (bottom - top >= 1) {
            this.margins = new Margins(top, bottom);
            this.cursor_position();
        }
    },
    set_charset: function(code, mode) {
        var charsetmap = {"(": "g0_charset", ")": "g1_charset"};

        if (code in MAPS) {
            this[charsetmap[mode]] = MAPS[code];
        }
    },
    set_mode: function() {
        var modes = arguments;
        var kwargs = {}; // sorry, "private" mode is ignored this way
        var i, j;
        if (kwargs['private']) {
            var modes_tmp = [];
            for (i = 0; i < modes.length; i++) {
                modes_tmp.push(modes[i] << 5);
            }
            modes = modes_tmp;
        }

        for (i = 0; i < modes.length; i++) this.mode[modes[i]] = true;

        if (modes[DECCOLM]) {
            this.resize(null, 132);
            this.erase_in_display(2);
            this.cursor_position();
        }

        if (modes[DECOM]) this.cursor_position();

        if (modes[DECSCNM]) {
            for (i = 0; i < this.lines; i++) {
                for (j = 0; j < this.columns; j++) {
                    this[i][j].reverse = true;
                }
            }
            this.select_graphic_rendition(_SGR["+reverse"]);
        }

        if (modes[DECTCEM]) this.cursor.hidden = false;
    },
    reset_mode: function() {
        var modes = arguments;
        var kwargs = {}; // sorry, "private" mode is ignored this way

        var i, j;
        if (kwargs['private']) {
            var modes_tmp = [];
            for (i = 0; i < modes.length; i++) {
                modes_tmp.push(modes[i] << 5);
            }
            modes = modes_tmp;
        }

        for (i = 0; i < modes.length; i++) this.mode[modes[i]] = false;

        if (modes[DECCOLM]) {
            this.resize(null, 80);
            this.erase_in_display(2);
            this.cursor_position();
        }

        if (modes[DECOM]) this.cursor_position();

        if (modes[DECSCNM]) {
            for (i = 0; i < this.lines; i++) {
                for (j = 0; j < this.columns; j++) {
                    this[i][j].reverse = false;
                }
            }
            this.select_graphic_rendition(_SGR["-reverse"]);
        }

        if (modes[DECTCEM]) this.cursor.hidden = true;
    },
    shift_in: function() {
        this.charset = 0;
    },
    shift_out: function() {
        this.charset = 1;
    },
    draw: function(ch) {
        var translate_tbl = [this.g0_charset,this.g1_charset][this.charset];
        ch = translate_tbl[ch.charCodeAt(0)] || ch;

        if (this.cursor.x == this.columns) {
            if (this.mode[DECAWM]) this.linefeed();
            else this.cursor.x -= 1;
        }

        if (this.mode[IRM]) this.insert_characters(1);

        this[this.cursor.y][this.cursor.x] = new Char(ch, this.cursor.attrs);

        this.cursor.x += 1;
    },
    carriage_return: function() {
        this.cursor.x = 0;
    },
    insert: function(idx, el) {
        Array.prototype.splice.call(this, idx, 0, el);
    },
    pop: function(idx) {
        Array.prototype.splice.call(this, idx, 1);
    },
    push: function(item) {
        this.insert(this.length - 1, item);
    },
    index: function() {
        var top = this.margins.top, bottom = this.margins.bottom;

        if (this.cursor.y == bottom) {
            if (top == 0 && bottom == this.lines - 1) {
                var row = this[0];
                this.history.push(row);
            }
            this.pop(top);
            this.insert(bottom, repeat(this.columns, this.default_char));
        } else {
            this.cursor_down();
        }
    },
    reverse_index: function() {
        var top = this.margins.top, bottom = this.margins.bottom;

        if (this.cursor.y == top) {
            this.pop(bottom);
            this.insert(top, repeat(this.columns, this.default_char));
        } else {
            this.cursor_up();
        }
    },
    linefeed: function() {
        this.index();

        if (this.mode[LNM]) this.carriage_return();
    },
    /*
    Move to the next tab space, or the end of the screen if there
    aren't anymore left.
    note: this is a more optimized version than original implementation
    */
    tab: function() {
        for (var i = this.cursor.x + 1; i < this.columns - 1; i++) {
            if (this.tabstops[i]) break;
        }

        this.cursor.x = Math.min(i, this.columns - 1);
    },
    backspace: function() {
        this.cursor_back();
    },
    save_cursor: function() {
        var c = this.cursor;
        var cursor_copy = new Cursor(c.x, c.y, new Char(c.attrs.data, c.attrs));
        this.savepoints.push(
            new Savepoint(
                cursor_copy,
                this.g0_charset,
                this.g1_charset,
                this.charset,
                this.mode[DECOM],
                this.mode[DECAWM]
            )
        );
    },
    restore_cursor: function() {
        if (this.savepoints) {
            var savepoint = this.savepoints.pop();

            this.g0_charset = savepoint.g0_charset;
            this.g1_charset = savepoint.g1_charset;
            this.charset = savepoint.charset;

            if (savepoint.origin) this.set_mode(DECOM);
            if (savepoint.wrap) this.set_mode(DECAWM);

            this.cursor = savepoint.cursor;
            this.ensure_bounds(true);
        } else {
            this.reset_mode(DECOM);
            this.cursor_position();
        }
    },
    insert_lines: function(count) {
        count = count || 1;
        var top = this.margins.top, bottom = this.margins.bottom;

        if (top <= this.cursor.y && this.cursor.y <= bottom) {
            var line_min = this.cursor.y;
            var line_max = Math.min(bottom + 1, this.cursor.y + count);
            for (var i = line_min; i < line_max; i++) {
                this.pop(bottom);
                this.insert(i, repeat(this.columns, this.default_char));
            }

            this.carriage_return();
        }
    },
    delete_lines: function(count) {
        count = count || 1;
        var top = this.margins.top, bottom = this.margins.bottom;

        if (top <= this.cursor.y && this.cursor.y <= bottom) {
            var cnt = Math.min(bottom - self.cursor.y, count);
            for (var i = 0; i < cnt; i++) {
                this.pop(this.cursor.y);
                this.insert(bottom, repeat(this.columns, this.cursor.attrs));
            }
            this.carriage_return();
        }
    },
    insert_characters: function(count) {
        count = Math.min(this.columns - this.cursor.x, count || 1);
        for (var i = 0; i < count; i++) {
            this[this.cursor.y].splice(this.cursor.x, 0, this.cursor.attrs);
            this[this.cursor.y].pop();
        }
    },
    delete_characters: function(count) {
        count = Math.min(this.columns - this.cursor.x, count || 1);
        for (var i = 0; i < count; i++) {
            this[this.cursor.y].splice(this.cursor.x, 1);
            this[this.cursor.y].push(this.cursor.attrs);
        }
    },
    erase_characters: function(count) {
        count = count || 1;
        var max_pos = Math.min(this.cursor.x + count, this.columns);

        for (var column = this.cursor.x; column < max_pos; column++) {
            this[this.cursor.y][column] = this.cursor.attrs;
        }
    },
    erase_in_line: function(type_of) {
        type_of = type_of || 0;

        var interval = [
            range(this.cursor.x, this.columns),
            range(0, this.cursor.x + 1),
            range(0, this.columns)
        ][type_of];

        var column;
        for (var i = 0; i < interval.length; i++) {
            column = interval[i];
            this[this.cursor.y][column] = this.cursor.attrs;
        }
    },
    erase_in_display: function(type_of) {
        type_of = type_of || 0;

        var interval = [
            range(this.cursor.y + 1, this.lines),
            range(0, this.cursor.y),
            range(0, this.lines)
        ][type_of];

        var line;
        for (var i = 0; i < interval.length; i++) {
            line = interval[i];
            this[line] = repeat(this.columns, this.cursor.attrs);
        }

        if (type_of == 0 || type_of == 1) {
            this.erase_in_line(type_of);
        }
    },
    set_tab_stop: function() {
        this.tabstops[this.cursor.x] = true;
    },
    clear_tab_stop: function(type_of) {
        if (!type_of) {
            delete this.tabstops[this.cursor.x];
        } else if (type_of == 3) {
            this.tabstops = {};
        }
    },
    ensure_bounds: function(use_margins) {
        var top, bottom;
        if (use_margins || this.mode[DECOM]) {
            top = this.margins.top;
            bottom = this.margins.bottom;
        } else {
            top = 0;
            bottom = this.lines - 1;
        }

        this.cursor.x = Math.min(Math.max(0, this.cursor.x), this.columns - 1);
        this.cursor.y = Math.min(Math.max(top, this.cursor.y), bottom);
    },
    cursor_up: function(count) {
        this.cursor.y -= count || 1;
        this.ensure_bounds(true);
    },
    cursor_up1: function(count) {
        this.cursor_up(count);
        this.carriage_return();
    },
    cursor_down: function(count) {
        this.cursor.y += count || 1;
        this.ensure_bounds(true);
    },
    cursor_down1: function(count) {
        this.cursor_down(count);
        this.carriage_return();
    },
    cursor_back: function(count) {
        this.cursor.x -= count || 1;
        this.ensure_bounds();
    },
    cursor_forward: function(count) {
        this.cursor.x += count || 1;
        this.ensure_bounds();
    },
    cursor_position: function(line, column) {
        column = (column || 1) - 1;
        line = (line || 1) - 1;

        if (this.mode[DECOM]) {
            line += this.margins.top;

            if (!(this.margins.top <= line && line <= this.margins.bottom)) {
                return;
            }
        }

        this.cursor.x = column;
        this.cursor.y = line;
        this.ensure_bounds();
    },
    cursor_to_column: function(column) {
        this.cursor.x = (column || 1) - 1;
        this.ensure_bounds();
    },
    cursor_to_line: function(line) {
        this.cursor.y = (line || 1) - 1;

        if (this.mode[DECOM]) {
            this.cursor.y += this.margins.top;
        }

        this.ensure_bounds();
    },
    bell: function() {
        var el = document.getElementById('bell');
        if (el && el.tagName == 'AUDIO') el.play();
    },
    select_graphic_rendition: function() {
        var replace = {};
        var attrs = arguments || [0], attr;

        for (var i = 0; i < attrs.length; i++) {
            attr = attrs[i];

            if (FG[attr]) {
                replace["fg"] = FG[attr];
            } else if (BG[attr]) {
                replace["bg"] = BG[attr];
            } else if (TEXT[attr]) {
                attr = TEXT[attr];
                replace[attr.substr(1, attr.length - 1)] = attr.charAt(0)=="+";
            } else if (!attr) {
                replace = new Char(this.default_char.data, this.default_char);
            }
        }
        var curs = this.cursor;

        var newAttrs = new Char(curs.attrs.data, curs.attrs);
        for (var k in replace) newAttrs[k] = replace[k];
        curs.attrs = newAttrs;
    }
};
// -*- coding: utf-8 -*-
/*
    pyte.streams
    ~~~~~~~~~~~~

    This module provides three stream implementations with different
    features; for starters, here's a quick example of how streams are
    typically used:

    >>> import pyte
    >>>
    >>> class Dummy(object):
    ...     def __init__(self):
    ...         self.y = 0
    ...
    ...     def cursor_up(self, count=None):
    ...         self.y += count or 1
    ...
    >>> dummy = Dummy()
    >>> stream = pyte.Stream()
    >>> stream.attach(dummy)
    >>> stream.feed(u"\u001B[5A")  // Move the cursor up 5 rows.
    >>> dummy.y
    5

    :copyright: (c) 2011 by Selectel, see AUTHORS for more details.
    :license: LGPL, see LICENSE for more details.
*/

if (!Function.prototype.bind) {
    Function.prototype.bind = function (scope) {
        var _function = this;

        return function () {
            return _function.apply(scope, arguments);
        }
    };
}


function Stream() {
    /*
    A stream is a state machine that parses a stream of characters
        and dispatches events based on what it sees.

        .. note::

           Stream only accepts unicode strings as input, but if, for some
           reason, you need to feed it with byte strings, consider using
           :class:`~pyte.streams.ByteStream` instead.

        .. seealso::

            `man console_codes <http://linux.die.net/man/4/console_codes>`_
                For details on console codes listed bellow in :attr:`basic`,
                :attr:`escape`, :attr:`csi` and :attr:`sharp`.
    */

    this.__init__();
}

Stream.prototype = {
    //: Control sequences, which don't require any arguments.
    basic: {},
    //: non-CSI escape sequences.
    escape: {},
    //: "sharp" escape sequences -- ``ESC // <N>``.
    sharp: {},
    //: CSI escape sequences -- ``CSI P1;P2;...;Pn <fn>``.
    csi: {},
    __init__: function() {
        this.handlers = {
            "stream": this._stream.bind(this),
            "escape": this._escape.bind(this),
            "arguments": this._arguments.bind(this),
            "sharp": this._sharp.bind(this),
            "charset": this._charset.bind(this)
        };

        this.listeners = [];
        this.reset();
    },
    reset: function() {
        /* Reset state to ``"stream"`` and empty parameter attributes.*/
        this.state = "stream";
        this.flags = {};
        this.params = [];
        this.current = "";
    },
    consume: function(ch) {
        /* Consume a single unicode character and advance the state as
        necessary.
        */

        try {
            this.handlers[this.state](ch)
        } catch (e) {
            // DEBUG window.console && console.log(e);
            /* python code:
            except TypeError:
                pass
            except KeyError:
                if __debug__:
                    self.flags["state"] = self.state
                    self.flags["unhandled"] = char
                    self.dispatch("debug", *self.params)
                    self.reset()
                else:
                    raise
            */
        }
    },
    feed: function(chars) {
        for (var i = 0; i < chars.length; i++) {
            this.consume(chars.charAt(i))
        }
    },
    attach: function(screen, only) {
        if (only) throw new Error("'only' is not implemented")
        this.listeners.push(screen)
    },
    detach: function(screen) {
        throw new Error("Not implemented")
    },
    dispatch: function(event, args, kwargs) {
        args = args || [];
        kwargs = kwargs || {};
        var listener, handler;
        for (var i = 0; i < this.listeners.length; i++) {
            listener = this.listeners[i];
            handler = listener[event];
            if (!handler) continue;
            if (listener.__before__) listener.__before__(event);
            handler.apply(listener, args); // yes, we ignore this.flags by now
            if (listener.__after__) listener.__after__(event);
        }
        if (kwargs.reset === true || kwargs.reset === undefined) {
            this.reset();
        }
    },
    _stream: function(ch) {
        if (ch in this.basic) this.dispatch(this.basic[ch], []);
        else if(ch == ESC) this.state = "escape";
        else if(ch == CSI) this.state = "arguments";
        else if (ch != NUL && ch != DEL) this.dispatch("draw", [ch]);
    },
    _escape: function(ch) {
        if (ch == "#") {
            this.state = "sharp";
        } else if (ch == "[") {
            this.state = "arguments";
        } else if (ch == "(" || ch == ")") {
            this.state = "charset";
            this.flags["mode"] = ch;
        } else {
            this.dispatch(this.escape[ch]);
        }
    },
    _sharp: function(ch) {
        this.dispatch(this.sharp[ch]);
    },
    _charset: function(ch) {
        this.dispatch("set_charset", [ch]);
    },
    __isDigit: function(ch) {
        var code = ch.charCodeAt(0);
        return code > 47 && code < 58;
    },
    _arguments: function(ch) {
        /*
        Parse arguments of an escape sequence.

        All parameters are unsigned, positive decimal integers, with
        the most significant digit sent first. Any parameter greater
        than 9999 is set to 9999. If you do not specify a value, a 0
        value is assumed.

        .. seealso::

           `VT102 User Guide <http://vt100.net/docs/vt102-ug/>`_
               For details on the formatting of escape arguments.

           `VT220 Programmer Reference <http://http://vt100.net/docs/vt220-rm/>`_
               For details on the characters valid for use as arguments.
        */
        if (ch == "?") {
            this.flags['private'] = true;
        } else if (ch == BEL || ch == BS || ch == HT || ch == LF || ch == VT || ch == FF || ch == CR) {
            this.dispatch(this.basic[ch], [], {reset: false});
        } else if (ch == SP) {
            return;
        } else if (ch == CAN || ch == SUB) {
            this.dispatch("draw", ch);
            this.state = "stream";
        } else if (this.__isDigit(ch)) {
            this.current += ch;
        } else {
            this.params.push(Math.min(parseInt(this.current || 0), 9999));

            if (ch == ';') this.current = "";
            else           this.dispatch(this.csi[ch], this.params);
        }
    }
};

Stream.prototype.basic[BEL] = "bell";
Stream.prototype.basic[BS] = "backspace";
Stream.prototype.basic[HT] = "tab";
Stream.prototype.basic[LF] = "linefeed";
Stream.prototype.basic[VT] = "linefeed";
Stream.prototype.basic[FF] = "linefeed";
Stream.prototype.basic[CR] = "carriage_return";
Stream.prototype.basic[SO] = "shift_out";
Stream.prototype.basic[SI] = "shift_in";

Stream.prototype.escape[RIS] = "reset";
Stream.prototype.escape[IND] = "index";
Stream.prototype.escape[NEL] = "linefeed";
Stream.prototype.escape[RI] = "reverse_index";
Stream.prototype.escape[HTS] = "set_tab_stop";
Stream.prototype.escape[DECSC] = "save_cursor";
Stream.prototype.escape[DECRC] = "restore_cursor";

Stream.prototype.sharp[DECALN] = "alignment_display";

Stream.prototype.csi[ICH] = "insert_characters";
Stream.prototype.csi[CUU] = "cursor_up";
Stream.prototype.csi[CUD] = "cursor_down";
Stream.prototype.csi[CUF] = "cursor_forward";
Stream.prototype.csi[CUB] = "cursor_back";
Stream.prototype.csi[CNL] = "cursor_down1";
Stream.prototype.csi[CPL] = "cursor_up1";
Stream.prototype.csi[CHA] = "cursor_to_column";
Stream.prototype.csi[CUP] = "cursor_position";
Stream.prototype.csi[ED] = "erase_in_display";
Stream.prototype.csi[EL] = "erase_in_line";
Stream.prototype.csi[IL] = "insert_lines";
Stream.prototype.csi[DL] = "delete_lines";
Stream.prototype.csi[DCH] = "delete_characters";
Stream.prototype.csi[ECH] = "erase_characters";
Stream.prototype.csi[HPR] = "cursor_forward";
Stream.prototype.csi[VPA] = "cursor_to_line";
Stream.prototype.csi[VPR] = "cursor_down";
Stream.prototype.csi[HVP] = "cursor_position";
Stream.prototype.csi[TBC] = "clear_tab_stop";
Stream.prototype.csi[SM] = "set_mode";
Stream.prototype.csi[RM] = "reset_mode";
Stream.prototype.csi[SGR] = "select_graphic_rendition";
Stream.prototype.csi[DECSTBM] = "set_margins";
Stream.prototype.csi[HPA] = "cursor_to_column";


/* add the bind function if not present */
if (!Function.prototype.bind) {
	Function.prototype.bind = function(obj) {
        //console.log("Function.prototype.bind");
		var slice1 = [].slice,
		args = slice1.call(arguments, 1),
		self = this,
		nop = function () {},
		bound = function () {
			return self.apply( this instanceof nop ? this : ( obj || {} ),
							   args.concat( slice1.call(arguments) ) );
		};

		nop.prototype = self.prototype;

		bound.prototype = new nop();

		return bound;
	};
}

var TermVarHandler = null;
var TermVarIsMac = null;
var TermVarKeyRepState = null;
var TermVarKeyRepStr = null;

function Term(ca) {
    TermVarHandler = ca;
    TermVarIsMac = (navigator.userAgent.indexOf("Mac") >= 0) ? true : false;
    TermVarKeyRepState = 0;
    TermVarKeyRepStr = "";

	//this.handler = ca;
	//this.is_mac = (navigator.userAgent.indexOf("Mac") >= 0) ? true : false;
	//this.key_rep_state = 0;
	//TermVarKeyRepStr = "";
}
Term.prototype.open = function (termid) {

    //console.log("Term.prototype.open");

    lc_terminal_keymap_opened = true;

	document.addEventListener("keydown", keyDownHandler, false);
	document.addEventListener("keypress", keyPressHandler, false);

    if (blinkInterv != null) {
        return;
    }
    blinkInterv = setInterval(function() {
        var el = document.getElementById('cursor');
        if (!el) return;
        cursor_is_visible = !cursor_is_visible;
        el.style.backgroundColor = cursor_is_visible ? 'grey' : '';
    }, 500);
};
Term.prototype.stopEventHandler = function() {

    if (!lc_terminal_keymap_opened) {
        return;
    }

    //console.log("stopHandler");
    //document.removeEventListener("keydown", this._debug_test, false);
    document.removeEventListener("keydown", keyDownHandler, false);
    document.removeEventListener("keypress", keyPressHandler, false);

    lc_terminal_keymap_opened = false;
    clearInterval(blinkInterv);
    blinkInterv = null;
}

function keyDownHandler(ev) {
	//console.log("Term.prototype.keyDownHandler");
    var seq;
	seq = "";
	switch (ev.keyCode) {
		case 8: // backspace
			seq = "\x08";
			break;
		case 9: // tab
			seq = "\t";
			break;
		case 13: // enter
			seq = "\r";
			break;
		case 27: // esc
			seq = "\x1b";
			break;
		case 37: // arrow left
			if (ev.altKey) seq = "\x1bb";
			else		   seq = "\x1b[D";
			break;
		case 39: // arrow right
			if (ev.altKey) seq = "\x1bf";
			else		   seq = "\x1b[C";
			break;
		case 38: // arrow up
			seq = "\x1b[A";
			break;
		case 40: // arrow down
			seq = "\x1b[B";
			break;
		case 46: // delete
			seq = "\x1b[3~";
			break;
		case 45: // insert
			seq = "\x1b[2~";
			break;
		case 36: // home
			seq = "\x1bOH";
			break;
		case 35: // end
			seq = "\x1bOF";
			break;
		case 33: // page up
			seq = "\x1b[5~";
			break;
		case 34: // page down
			seq = "\x1b[6~";
			break;
		case 112: // F1
			seq = "\x1b[[A";
			break;
		case 113: // F2
			seq = "\x1b[[B";
			break;
		case 114: // F3
			seq = "\x1b[[C";
			break;
		case 115: // F4
			seq = "\x1b[[D";
			break;
		case 116: // F5
			seq = "\x1b[15~";
			break;
		case 117: // F6
			seq = "\x1b[17~";
			break;
		case 118: // F7
			seq = "\x1b[18~";
			break;
		case 119: // F8
			seq = "\x1b[19~";
			break;
		case 120: // F9
			seq = "\x1b[20~";
			break;
		case 121: // F10
			seq = "\x1b[21~";
			break;
		case 122: // F11
			seq = "\x1b[23~";
			break;
		case 123: // F12
			seq = "\x1b[24~";
			break;
		default:
			if (ev.ctrlKey) {
				if (ev.keyCode >= 65 && ev.keyCode <= 90) {
					seq = String.fromCharCode(ev.keyCode - 64);
				} else if (ev.keyCode == 32) {
					seq = String.fromCharCode(0);
				}
			} else if ((!TermVarIsMac && ev.altKey) || (TermVarIsMac && ev.metaKey)) {
				if (ev.keyCode >= 65 && ev.keyCode <= 90) {
					seq = "\x1b" + String.fromCharCode(ev.keyCode + 32);
				}
			}
			break;
	}
	if (seq) {
		if (ev.stopPropagation) ev.stopPropagation();
		if (ev.preventDefault) ev.preventDefault();
		TermVarKeyRepState = 1;
		TermVarKeyRepStr = seq;
		TermVarHandler(seq);
		return false;
	} else {
		TermVarKeyRepState = 0;
		return true;
	}
};
function keyPressHandler(ev) {
    //console.log("Term.prototype.keyPressHandler");
	var tagName = ev.target && ev.target.tagName;
	if (tagName == 'INPUT' || tagName == 'TEXTAREA') return;

	var seq, code;
	if (ev.stopPropagation) ev.stopPropagation();
	if (ev.preventDefault) ev.preventDefault();
	seq = "";
	if (!("charCode" in ev)) {
		code = ev.keyCode;
		if (TermVarKeyRepState == 1) {
			TermVarKeyRepState = 2;
			return false;
		} else if (TermVarKeyRepState == 2) {
			TermVarHandler(TermVarKeyRepStr);
			return false;
		}
	} else {
		code = ev.charCode;
	}
	if (code != 0) {
		if (!ev.ctrlKey && ((!TermVarIsMac && !ev.altKey) || (TermVarIsMac && !ev.metaKey))) {
			seq = String.fromCharCode(code);
		}
	}
	if (seq) {
		TermVarHandler(seq);
		return false;
	} else {
		return true;
	}
};



function indent(str, len) {
	str = '' + str
	while (str.length < 8) str += ' '
	return str
}

function paste() {
	var el = document.getElementById('paste-buf');
	send_cmd(el.value+"\n");
	el.value = '';
	//--try { document.body.focus(); } catch(e) { }
	//--try { document.focus(); } catch(e) { }
	//--try { window.focus(); } catch(e) { }
}

var cursor_is_visible = true;
var cursor = {x: 0, y: 0};
var blinkInterv = null;/*setInterval(function() {
	var el = document.getElementById('cursor');
	if (!el) return;
	cursor_is_visible = !cursor_is_visible;
	el.style.backgroundColor = cursor_is_visible ? 'grey' : '';
}, 1000);*/

var html_esc = {
	'<': '&lt;',
	'>': '&gt;',
	'&': '&amp;',
	' ': '&nbsp;'
};

var drawn_lines = [];
var fg_color_map = {
	'default': 'default',
	'black': 'black',
	'red': '#ff0000',
	'green': '#00ff00',
	'brown': '#ffc709',
	'blue': '#006fb8',
	'magenta': '#ff00ff',
	'cyan': '#2cb5e9',
	'white': '#ffffff'
};
var bg_color_map = {
	'default': 'black',
	'black': 'black',
	'red': '#ff0000',
	'green': '#39b54a',
	'brown': '#ffc709',
	'blue': '#006fb8',
	'magenta': '#ff00ff',
	'cyan': '#2cb5e9',
	'white': '#ffffff'
};
var default_fg_bg = {
	'default': 'white',
	'black': 'white',
	'red': 'black',
	'green': 'black',
	'brown': 'white',
	'blue': 'black',
	'magenta': 'black',
	'cyan': 'black',
	'white': 'black'
};

window.LessTerminal = (function() {
    
    "use strict";

    var insts = {};

    var instCtrlCurrent = null;

    function LessTerminal(termid, wsurl) {

    }

})();

var lc_terminal_keymap_opened = false;
var lc_terminal_scr = null;
var lc_terminal_ws = null;
function lc_terminal_conn(termid, wsurl)
{
    "use strict";
    var domobj = document.getElementById(termid);    

    var window_cols_rows = function()
    {
        var winW = 630, winH = 460;
        if (domobj && domobj.offsetWidth) {
            winW = domobj.offsetWidth;
            winH = domobj.offsetHeight;
        }
   
        return [Math.floor(winW / 9), Math.floor( winH / 16)]
    }

    var get_line_html = function(chars, line)
    {
        var fg, bg;
        var res = ['<span>'];
        var ch;
        var prev_style = '', style, i;
        for (i = 0; i < chars.length; i++) {
            ch = chars[i];
    
            style = '';
            if (ch.reverse) {
                fg = fg_color_map[ch.bg];
                bg = bg_color_map[ch.fg];
                if (bg == 'default') bg = default_fg_bg[fg];
            } else {
                fg = fg_color_map[ch.fg];
                bg = bg_color_map[ch.bg];
                if (fg == 'default') fg = default_fg_bg[bg];
            }
            if (fg == 'black' && bg == 'black') fg = 'gray';
    
            if (fg != 'white') style += 'color: ' + fg + '; ';
            if (bg != 'black') style += 'background-color: ' + bg + '; ';
    
            if (ch.bold) style += 'font-weight: bold; ';
            if (ch.italics) style += 'font-style: italic; ';
            if (ch.underscore || ch.strikethrough) {
                style += 'font-decoration: '+(ch.underscore ? 'underline ' : '')+' '+(ch.underscore ? 'line-through ' : '')+'; ';
            }
    
            if (style != prev_style) {
                res.push('</span><span style="' + style + '">');
            }
    
            if (cursor.x == i && cursor.y == line) res.push('<span id="cursor" style="background-color: grey;">');
    
            res.push(html_esc[ch.data] || ch.data);
    
            if (cursor.x == i && cursor.y == line) res.push('</span>');
    
            prev_style = style;
        }
        res.push('</span>');
    
        return res.join('');
    }
    
    
    var redraw_line = function(screen, drawn_line)
    {
        var line = drawn_line + scrollOffset;
        cursor = screen.cursor;
        var el = document.getElementById('row' + drawn_line);
        var chars = line >= 0 ? screen[line] : screen.history[screen.history.length + line];
        var line_html = get_line_html(chars, line);
    
        if (el && (!drawn_lines[drawn_line] || drawn_lines[drawn_line] != line_html)) {
            el.innerHTML = line_html;
        }
        drawn_lines[drawn_line] = line_html;
    }
    
    var handle_scroll = function(screen, delta)
    {
        scrollOffset += delta;
        scrollOffset = Math.min(0, Math.max(-screen.history.length, scrollOffset));
        newData = true;
    }

    var _resize = function(scr, ws, initonly)
    {
        var colsrows = window_cols_rows();
        var rows = [];
        for(var i = 0; i < colsrows[1]; i++) {
            rows.push("<div id='row" + i + "' class='outputrow'>&nbsp;</div>")
        }

        domobj.innerHTML = rows.join("\n");
        if (!initonly) {
            scr.resize(colsrows[1], colsrows[0])
            drawn_lines = []
            newData = true;
    
            ws.send('w' + indent(colsrows[0], 8) + indent(colsrows[1], 8))  
        }
    }
    
    var colsrows = window_cols_rows();
    var newData = false;
    var scrollOffset = 0;
    var stream = new Stream();
    lc_terminal_scr = new Screen(colsrows[0], colsrows[1]);
    stream.attach(lc_terminal_scr);

    domobj.addEventListener('mousewheel', function (e) {
        var delta = e.wheelDeltaY || e.wheelDelta;
        handle_scroll(lc_terminal_scr, -delta);
    });

    domobj.addEventListener('MozMousePixelScroll', function (e) {
        if (!e.VERTICAL_AXIS) return;
        handle_scroll(lc_terminal_scr, e.detail);
    });

    lc_terminal_ws = new WebSocket(wsurl, "term");

    lc_terminal_ws.onopen = function() {
        var req = {
            "access_token": lessCookie.Get("access_token"),
        }
        lc_terminal_ws.send(JSON.stringify(req))
        lc_terminal_ws.send(indent(colsrows[0], 8))
        lc_terminal_ws.send(indent(colsrows[1], 8))
    }
    lc_terminal_ws.onmessage = function(ev) {
        stream.feed(ev.data)
        newData = true
    }
    lc_terminal_ws.onclose = function() {
        stream.feed("Connection closed\n")
        newData = true

        term.stopEventHandler();

        //clearInterval(blinkInterv);
        //--window.close();
        //--window.name = "closed";
    }
    
    var term = new Term(send_cmd);
    //term.open(termid);

    domobj.onmousedown = function(ev) {
        term.open(termid);
    }
    $("#"+ termid).mouseleave(function() {
        term.stopEventHandler();
    });
    
    function send_cmd(val) {
        //console.log("send_cmd:"+ val);
        lc_terminal_ws.send('i' + indent(string_utf8_len(val + ''), 8) + val)
    }

    lc_terminal_conn.SendCmd = function(val)
    {
        send_cmd(val);
    }
    
    function redraw() {
        for (var i = 0; i < lc_terminal_scr.lines; i++) {
            redraw_line(lc_terminal_scr, i);
        }
    }
    
    var redDataInterv = setInterval(function() {
        //console.log("Asdfasd");
        if (newData) {
            redraw()
            newData = false
        }
    }, 16);
    

    var uiCheckInterv = setInterval(function() {
        
        if (document.getElementById(termid)) {
            return;
        }

        lc_terminal_ws.close();
        
        clearInterval(redDataInterv);
        clearInterval(uiCheckInterv);

    }, 3000);

    lc_terminal_conn.Resize = function()
    {
        if (!document.getElementById(termid)) {
            return;
        }

        _resize(lc_terminal_scr, lc_terminal_ws);
    }

    lc_terminal_conn.IsOk = function() {
        if (!document.getElementById(termid)) {
            return false;
        }

        if (lc_terminal_ws == null) {
            return false;
        }

        return true;
    }

    lc_terminal_conn.CloseAll = function() {
        term.stopEventHandler();
        lc_terminal_ws.close();
    }

    _resize(lc_terminal_scr, lc_terminal_ws, true);
}

/*
function get_line_html(chars, line)
{
	var fg, bg;
	var res = ['<span>'];
	var ch;
	var prev_style = '', style, i;
	for (i = 0; i < chars.length; i++) {
		ch = chars[i];

		style = '';
		if (ch.reverse) {
			fg = fg_color_map[ch.bg];
			bg = bg_color_map[ch.fg];
			if (bg == 'default') bg = default_fg_bg[fg];
		} else {
			fg = fg_color_map[ch.fg];
			bg = bg_color_map[ch.bg];
			if (fg == 'default') fg = default_fg_bg[bg];
		}
		if (fg == 'black' && bg == 'black') fg = 'gray';

		if (fg != 'white') style += 'color: ' + fg + '; ';
		if (bg != 'black') style += 'background-color: ' + bg + '; ';

		if (ch.bold) style += 'font-weight: bold; ';
		if (ch.italics) style += 'font-style: italic; ';
		if (ch.underscore || ch.strikethrough) {
			style += 'font-decoration: '+(ch.underscore ? 'underline ' : '')+' '+(ch.underscore ? 'line-through ' : '')+'; ';
		}

		if (style != prev_style) {
			res.push('</span><span style="' + style + '">');
		}

		if (cursor.x == i && cursor.y == line) res.push('<span id="cursor" style="background-color: grey;">');

		res.push(html_esc[ch.data] || ch.data);

		if (cursor.x == i && cursor.y == line) res.push('</span>');

		prev_style = style;
	}
	res.push('</span>');

	return res.join('');
}

function redraw_line(screen, drawn_line) {

	var line = drawn_line + scrollOffset;
	cursor = screen.cursor;
	var el = document.getElementById('row' + drawn_line);
	var chars = line >= 0 ? screen[line] : screen.history[screen.history.length + line];
	var line_html = get_line_html(chars, line);

	if (!drawn_lines[drawn_line] || drawn_lines[drawn_line] != line_html) el.innerHTML = line_html;
	drawn_lines[drawn_line] = line_html;
}

function handle_scroll(screen, delta) {
	scrollOffset += delta;
	scrollOffset = Math.min(0, Math.max(-screen.history.length, scrollOffset));
	newData = true;
}

function window_cols_rows(termid) {
	var domobj = document.getElementById(termid);
    var winW = 630, winH = 460;
	if (domobj && domobj.offsetWidth) {
		winW = domobj.offsetWidth;
		winH = domobj.offsetHeight;
	}
	if (document.compatMode == 'CSS1Compat' && document.documentElement && document.documentElement.offsetWidth) {
		winW = document.documentElement.offsetWidth;
		winH = document.documentElement.offsetHeight;
	}
	if (window.innerWidth && window.innerHeight) {
		winW = window.innerWidth;
		winH = window.innerHeight;
	}

	return [Math.floor(winW / 9), Math.floor( winH / 16)]
}

function resize(termid, scr, ws, initonly) {
	var domobj = document.getElementById(termid);
    var colsrows = window_cols_rows(termid);
	var rows = [];
	for(var i = 0; i < colsrows[1]; i++) {
		rows.push("<div id='row" + i + "' class='outputrow'>&nbsp;</div>");
	}
	domobj.innerHTML = rows.join("\n")
	if (!initonly) {
		scr.resize(colsrows[1], colsrows[0])
		drawn_lines = []
		newData = true;

		ws.send('w' + indent(colsrows[0], 8) + indent(colsrows[1], 8))
	}
}
*/
function string_utf8_len(str) {
	var len = 0, l = str.length;

	for (var i = 0; i < l; i++) {
		var c = str.charCodeAt(i);
		if (c <= 0x0000007F) len++;
		else if (c >= 0x00000080 && c <= 0x000007FF) len += 2;
		else if (c >= 0x00000800 && c <= 0x0000FFFF) len += 3;
		else len += 4;
	}

	return len;
}
