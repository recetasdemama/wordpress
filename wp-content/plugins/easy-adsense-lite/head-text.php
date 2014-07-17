<?php

/*
  Copyright (C) 2008 www.ads-ez.com

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License as
  published by the Free Software Foundation; either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

echo '<td style="width:30%">';

if (rand(0, 2) % 2
        || $ez->slug == "easy-ads"
        || $ez->slug == "easy-chitika"
        || $ez->slug == "google-adsense") {
  $ez->renderSupportText();
}
else {
  $ez->renderAffiliate();
}
$ez->renderTipDivs();

echo '</td>';
echo '<td style="width:30%">';

$ez->renderProText();

echo '</td>';
