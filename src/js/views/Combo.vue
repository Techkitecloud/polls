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
	<AppContent>
		<div class="poll-header-buttons">
			<ActionToggleSidebar />
		</div>
		<div class="combo-title">
			{{ title }}
		</div>

		<div v-if="description" class="area__header">
			{{ description }}
		</div>

		<div class="area__main">
			<ComboTable v-show="polls.length" />

			<EmptyContent v-if="!polls.length" icon="icon-polls">
				{{ t('polls', 'No polls selected') }}
				<template #desc>
					{{ t('polls', 'Select polls by clicking on them in the right sidebar!') }}
				</template>
			</EmptyContent>
		</div>

		<LoadingOverlay v-if="isLoading" />
	</AppContent>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import { AppContent, EmptyContent } from '@nextcloud/vue'
import { emit } from '@nextcloud/event-bus'
import ComboTable from '../components/Combo/ComboTable'
import ActionToggleSidebar from '../components/Actions/ActionToggleSidebar'

export default {
	name: 'Combo',
	components: {
		AppContent,
		ComboTable,
		EmptyContent,
		ActionToggleSidebar,
		LoadingOverlay: () => import('../components/Base/LoadingOverlay'),
	},

	data() {
		return {
			isLoading: false,
			title: t('polls', 'Combined polls'),
			description: t('polls', 'Combine multiple date polls in a single view'),
		}
	},

	computed: {
		...mapGetters({
			pollCombo: 'combo/pollCombo',
		}),
		...mapState({
			polls: (state) => state.combo.polls,
			savePollCombo: (state) => state.settings.user.pollCombo,
		}),

		/* eslint-disable-next-line vue/no-unused-properties */
		windowTitle() {
			return `${t('polls', 'Polls')} - ${this.title}`
		},
	},

	watch: {
		pollCombo() {
			this.setPollCombo({ pollCombo: this.pollCombo })
		},
		savePollCombo() {
			this.verifyPolls()
		},
	},

	created() {
		// simulate @media:prefers-color-scheme until it is supported for logged in users
		// This simulates the theme--dark
		// TODO: remove, when completely supported by core
		if (!window.matchMedia) {
			return true
		}
		emit('polls:sidebar:toggle', { open: (window.innerWidth > 920) })
		this.verifyPolls()
	},

	beforeDestroy() {
		// this.$store.dispatch({ type: 'combo/reset' })
	},

	methods: {
		...mapActions({
			setPollCombo: 'settings/setPollCombo',
			verifyPolls: 'combo/verifyPollsFromSettings',
		}),
	},

}

</script>

<style lang="scss">
	.combo-title {
		font-weight: bold;
		font-size: 1.3em;
		line-height: 1.5em;
	}

	.poll-header-buttons {
		display: flex;
		flex-wrap: wrap-reverse;
		flex: 0 1 auto;
		justify-content: flex-end;
		align-self: flex-end;
		border-radius: var(--border-radius-pill);
	}
</style>
