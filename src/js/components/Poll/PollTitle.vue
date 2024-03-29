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

<template lang="html">
	<div class="poll-title">
		<div class="poll-title__title">
			{{ title }}
		</div>
		<div v-if="showSubText" class="poll-title__sub">
			<span>{{ subTextLeft }}</span>
			<span v-for="(subText) in subTexts"
				:key="subText.text">
				<span> | </span>
				<span :class="[subText.class, subText.icon]">{{ subText.text }}</span>
			</span>
			<button v-if="isNoAccessSet" @click="openSharing">
				{{ t('polls', 'Grant access') }}
			</button>
		</div>
	</div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import { emit } from '@nextcloud/event-bus'
import moment from '@nextcloud/moment'

export default {
	name: 'PollTitle',

	props: {
		showSubText: {
			type: Boolean,
			default: false,
		},
	},

	computed: {
		...mapState({
			access: (state) => state.poll.access,
			title: (state) => state.poll.title,
			expire: (state) => state.poll.expire,
			isDeleted: (state) => state.poll.deleted,
			ownerDisplayName: (state) => state.poll.owner.displayName,
			pollCreated: (state) => state.poll.created,
			mayEdit: (state) => state.poll.acl.allowEdit,
		}),

		...mapGetters({
			isClosed: 'poll/isClosed',
			hasShares: 'shares/hasShares',
			proposalsExpirySet: 'poll/proposalsExpirySet',
			proposalsExpired: 'poll/proposalsExpired',
			proposalsExpireRelative: 'poll/proposalsExpireRelative',
		}),

		subTextLeft() {
			return t('polls', 'A poll from {name}', { name: this.ownerDisplayName })
		},

		isNoAccessSet() {
			return this.access === 'hidden' && !this.hasShares && this.mayEdit
		},

		subTexts() {
			const subTexts = []

			if (this.isNoAccessSet) {
				return [{
					text: t('polls', 'Currently no users have access to this poll'),
					icon: 'icon-error',
					class: 'closed',
				}]
			}
			if (this.isDeleted) {
				return [{
					text: t('polls', 'Archived'),
					icon: 'icon-category-app-bundles',
					class: 'archived',
				}]
			}

			if (this.isClosed) {
				return [{
					text: this.timeExpirationRelative,
					icon: 'icon-polls-closed',
					class: 'closed',
				}]
			}

			if (!this.isClosed && this.expire) {
				subTexts.push({
					text: t('polls', 'Closing {relativeExpirationTime}', { relativeExpirationTime: this.timeExpirationRelative }),
					icon: 'icon-calendar-000',
					class: this.closeToClosing ? 'closing' : 'open',
				})
			}

			if (this.proposalsExpirySet && this.proposalsExpired) {
				subTexts.push({
					text: t('polls', 'Proposal period ended {timeRelative}', { timeRelative: this.proposalsExpireRelative }),
					icon: 'icon-add',
					class: 'proposal',
				})
			}

			if (this.proposalsExpirySet && !this.proposalsExpired) {
				subTexts.push({
					text: t('polls', 'Proposal period ends {timeRelative}', { timeRelative: this.proposalsExpireRelative }),
					icon: 'icon-add',
					class: 'proposal',
				})
			}

			if (subTexts.length < 1) {
				return [{
					text: this.dateCreatedRelative,
					icon: 'icon-clock',
					class: 'created',
				}]
			}
			return subTexts
		},

		dateCreatedRelative() {
			return moment.unix(this.pollCreated).fromNow()
		},

		closeToClosing() {
			return (!this.isClosed && this.expire && moment.unix(this.expire).diff() < 86400000)
		},

		timeExpirationRelative() {
			if (this.expire) {
				return moment.unix(this.expire).fromNow()
			}
			return t('polls', 'never')

		},
	},

	methods: {
		openSharing() {
			emit('polls:sidebar:toggle', { open: true, activeTab: 'sharing' })
		},
	},
}

</script>

<style lang="scss">
	.poll-title {
		.poll-title__sub {
			opacity: 0.7;
			line-height: 1.2em;
			font-size: 1em;
			button {
				margin-left: 8px;
			}

			[class^='icon-'], [class*=' icon-'] {
				padding-left: 21px;
				background-position: left center;
			}
			.closed {
				color: var(--color-error);
				font-weight: 700;
			}
			.closing {
				color: var(--color-warning);
				font-weight: 700;
			}
			.open {
				font-weight: 700;
			}
			.archived {
				color: var(--color-error);
				font-weight: 700;
			}
			.created {
				color: var(--color-text-light);
			}
		}

		.poll-title__title {
			font-weight: bold;
			font-size: 1.3em;
			line-height: 1.5em;
		}
	}
</style>
