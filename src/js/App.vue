<!--
  - @copyright Copyright (c) 2018 René Gieling <github@dartcafe.de>
  -
  - @author René Gieling <github@dartcafe.de>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<div id="app-polls">
		<router-view />
	</div>
</template>

<script>
// import moment from 'moment'
// import lodash from 'lodash'
// import pollItem from './components/pollItem'
// import pollListHeader from './components/pollListHeader'

export default {
	name: 'App'

	// components: {
	// 	pollItem,
	// 	pollListHeader
	// }
}
</script>

<style  lang="scss">
#app-polls {
	width: 100%;
	display: flex;
}

#app-content {
    width: 100%;
    display: flex;
}
</style>