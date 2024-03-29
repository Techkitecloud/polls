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
	<div class="poll-header-buttons">
		<UserMenu />
		<Popover>
			<template #trigger>
				<Actions>
					<ActionButton icon="icon-info">
						{{ t('polls', 'Poll informations') }}
					</ActionButton>
				</Actions>
			</template>
			<PollInformation />
		</Popover>
		<ActionSortOptions />
		<ActionChangeView />
		<ExportPoll v-if="allowPollDownload" />
		<ActionToggleSidebar v-if="allowEdit || allowComment" />
	</div>
</template>

<script>
import { mapState } from 'vuex'
import { Actions, ActionButton, Popover } from '@nextcloud/vue'
import { emit } from '@nextcloud/event-bus'
import ActionSortOptions from '../Actions/ActionSortOptions'
import ActionChangeView from '../Actions/ActionChangeView'
import ActionToggleSidebar from '../Actions/ActionToggleSidebar'
import UserMenu from '../User/UserMenu'

export default {
	name: 'PollHeaderButtons',
	components: {
		Actions,
		ActionButton,
		Popover,
		ActionChangeView,
		ActionSortOptions,
		ActionToggleSidebar,
		ExportPoll: () => import('../Export/ExportPoll'),
		PollInformation: () => import('../Poll/PollInformation'),
		UserMenu,
	},

	computed: {
		...mapState({
			allowComment: (state) => state.poll.allowComment,
			allowEdit: (state) => state.poll.acl.allowEdit,
			allowPollDownload: (state) => state.poll.acl.allowPollDownload,
		}),
	},

	created() {
		emit('polls:sidebar:toggle', { open: (window.innerWidth > 920) })
	},

	beforeDestroy() {
		this.$store.dispatch({ type: 'poll/reset' })
	},
}

</script>

<style lang="scss">
.poll-header-buttons {
	display: flex;
	flex-wrap: wrap-reverse;
	flex: 0 1 auto;
	justify-content: flex-end;
	align-self: flex-end;
	border-radius: var(--border-radius-pill);
}

.icon.icon-settings.active {
	display: block;
	width: 44px;
	height: 44px;
}

</style>
