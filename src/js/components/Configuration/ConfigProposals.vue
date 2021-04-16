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
	<div>
		<RadioGroupDiv v-model="allowProposals" :options="proposalsOptions" />
		<CheckBoxDiv v-show="proposalsAllowed" v-model="pollExpiration" :label="t('polls', 'Closing Date')" />
		<DatetimePicker v-show="pollExpiration && proposalsAllowed" v-model="pollExpire" v-bind="expirationDatePicker" />
	</div>
</template>

<script>
import debounce from 'lodash/debounce'
import { mapState, mapGetters } from 'vuex'
import { showSuccess, showError } from '@nextcloud/dialogs'
import { emit } from '@nextcloud/event-bus'
import moment from '@nextcloud/moment'
import { DatetimePicker } from '@nextcloud/vue'
import CheckBoxDiv from '../Base/CheckBoxDiv'
import RadioGroupDiv from '../Base/RadioGroupDiv'

export default {
	name: 'ConfigProposals',

	components: {
		CheckBoxDiv,
		DatetimePicker,
		RadioGroupDiv,
	},

	data() {
		return {
			titleEmpty: false,
		}
	},

	computed: {
		...mapState({
			poll: state => state.poll,
		}),

		...mapGetters({
			proposalsAllowed: 'poll/proposalsAllowed',
			proposalsOptions: 'poll/proposalsOptions',
		}),

		// Add bindings
		allowProposals: {
			get() {
				return this.poll.allowProposals
			},
			set(value) {
				this.writeValue({ allowProposals: value })
			},
		},

		pollExpire: {
			get() {
				return moment.unix(this.poll.proposalsExpire)._d
			},
			set(value) {
				this.writeValue({ proposalsExpire: moment(value).unix() })
			},
		},

		pollExpiration: {
			get() {
				return this.poll.proposalsExpire
			},
			set(value) {
				if (value) {
					this.writeValue({ proposalsExpire: moment().add(1, 'week').unix() })
				} else {
					this.writeValue({ proposalsExpire: 0 })
				}
			},
		},

		expirationDatePicker() {
			return {
				editable: true,
				minuteStep: 5,
				type: 'datetime',
				format: moment.localeData().longDateFormat('L LT'),
				placeholder: t('polls', 'Proposals possible until'),
				confirm: true,
				lang: {
					formatLocale: {
						firstDayOfWeek: this.firstDOW,
						months: moment.months(),
						monthsShort: moment.monthsShort(),
						weekdays: moment.weekdays(),
						weekdaysMin: moment.weekdaysMin(),
					},
				},
			}
		},
	},

	methods: {
		successDebounced: debounce(function(title) {
			showSuccess(t('polls', '"{pollTitle}" successfully saved', { pollTitle: title }))
			emit('update-polls')
		}, 1500),

		writeValue(e) {
			this.$store.commit('poll/setProperty', e)
			this.updatePoll()
		},

		async updatePoll() {
			if (this.titleEmpty) {
				showError(t('polls', 'Title must not be empty!'))
			} else {
				try {
					await this.$store.dispatch('poll/update')
					this.successDebounced(this.poll.title)
				} catch {
					showError(t('polls', 'Error writing poll'))
				}
			}
		},

	},
}
</script>