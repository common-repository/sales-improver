<div class="notice notice-success is-dismissible">
	<div class="notice-wrap">
		<div class="row">
			<div class="col-<?php echo ( (($license['type'] ?? 'trial') == 'trial') ? 12 : 7 ); ?>">
				<h4 class="my-0">Provide your e-mail address to get an offer for the premium plugin.</h4>
                <br />
				<form method="post" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" class="input-group email-section">
					<input type="hidden" name="action" value="license_notice">
					<div class="input-group-prepend">
						<span class="input-group-text" id="basic-addon1">@</span>
					</div>
					<input type="email" name="email" class="form-control" required>
					<div class="input-group-append">
						<button type="submit" class="button button-primary">SUBMIT</button>
					</div>
                    <p class="email-section-notice d-none" style="color: #135e96; font-weight: bold;">Your request has been received. We will notify you!</p>
				</form>
			</div>
            <?php if(($license['type'] ?? 'trial') == 'subscriber'): ?>
                <div class="col-5">
                    <div>License: <strong id="license"><?php echo esc_attr($license['license'] ?? ''); ?></strong></div>
                    <div>Expiration Date: <strong id="expiration"><?php echo esc_attr( wp_date('d F Y', strtotime($license['expiration'] ?? 'now')) ); ?></strong></div>
                    <div>Last Validated: <strong id="last_valid"><?php echo esc_attr( wp_date('d F Y', strtotime($license['last_check'] ?? 'now')) ); ?></strong></div>
                </div>
            <?php endif; ?>
		</div>
	</div>
</div>