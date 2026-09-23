class PaymentInfo {
  final int paymentId;
  final String transactionReference;
  final double amount;
  final String currency;
  final String? checkoutUrl;
  final String status;

  const PaymentInfo({
    required this.paymentId,
    required this.transactionReference,
    required this.amount,
    required this.currency,
    this.checkoutUrl,
    required this.status,
  });

  factory PaymentInfo.fromJson(Map<String, dynamic> json) {
    return PaymentInfo(
      paymentId: json['payment_id'] as int,
      transactionReference: json['transaction_reference'] as String,
      amount: (json['amount'] as num).toDouble(),
      currency: json['currency'] as String? ?? 'YER',
      checkoutUrl: json['checkout_url'] as String?,
      status: json['status'] as String? ?? 'Pending',
    );
  }
}
